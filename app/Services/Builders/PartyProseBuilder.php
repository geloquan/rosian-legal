<?php

namespace App\Services\Builders;

use App\Models\DeedOfAbsoluteSaleDocumentPartyMember;
use App\Services\Builders\Concerns\HasIndent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Element\TextRun;

abstract class PartyProseBuilder
{
  use HasIndent;

  abstract protected function label(): string;


  protected function bold(): array
  {
    return ['bold' => true];
  }

  protected function normal(): array
  {
    return ['bold' => false];
  }

  public function build(Collection $members, ?DeedOfAbsoluteSaleDocumentPartyMember $aif = null): TextRun
  {
    if ($members->isEmpty()) {
      Log::error('At least one principal party member is required.');

      return new TextRun();
    }

    return match(true) {
      $members->count() > 1 => $this->plural($members, $aif),
      default               => $this->singular($members->first(), $aif),
    };
  }

  // ---------------------------------------------------------------
  // FLORA RAYBOULD and FREDERICK PEDRO GLORIBA, both legal age,
  // Filipinos, and residents of Bacolod City Negros Occidental,
  // Philippines hereinafter referred to as the "VENDEE" represented
  // by his Attorney-in-Fact FREDERICK PEDRO GLORIBA;
  // ---------------------------------------------------------------
  private function plural(Collection $members, ?DeedOfAbsoluteSaleDocumentPartyMember $aif = null): TextRun
  {
    $principal = $members->first(fn ($m) => !str_ends_with($m->role->value, '-husband')
      && !str_ends_with($m->role->value, '-wife'));
    $spouse    = $members->first(fn ($m) => str_ends_with($m->role->value, '-husband')
      || str_ends_with($m->role->value, '-wife'));

    if ($principal && $spouse) {
      return $this->couple($principal, $spouse, $aif);
    }

    $textRun   = new TextRun();
    $textRun->addText("\t");
    $names     = $this->joinNames($members);
    $residence = $this->commonResidence($members);
    $pronoun   = $this->groupPronoun($members);
    $label     = $this->label();

    $textRun->addText($names, $this->bold());
    $textRun->addText(', both legal age, Filipinos, and residents of ', $this->normal());
    $textRun->addText($residence, $this->normal());
    $textRun->addText(', Philippines hereinafter referred to as the "', $this->normal());
    $textRun->addText($label, $this->bold());

    if ($aif) {
      $textRun->addText('" represented by ' . $pronoun . ' attorney-in-fact ', $this->normal());
      $textRun->addText(strtoupper($aif->name), $this->bold());
    } else {
      $textRun->addText('"', $this->normal());
    }

    $textRun->addText(';', $this->normal());

    return $textRun;
  }

  private function couple(
    DeedOfAbsoluteSaleDocumentPartyMember $principal,
    DeedOfAbsoluteSaleDocumentPartyMember $spouse,
    ?DeedOfAbsoluteSaleDocumentPartyMember $aif = null
  ): TextRun {
    $textRun = new TextRun();
    $textRun->addText("\t");
    $pronoun = $this->pronoun($principal);
    $label   = $this->label();

    $textRun->addText($principal->name, $this->bold());
    $textRun->addText(', of legal age, Filipino, married to ', $this->normal());
    $textRun->addText($spouse->name, $this->bold());
    $textRun->addText(', and a resident of ', $this->normal());
    $textRun->addText($this->residence($principal), $this->normal());
    $textRun->addText(', Philippines, hereinafter referred to as the "', $this->normal());
    $textRun->addText($label, $this->bold());

    if ($aif) {
      $textRun->addText('" represented by ' . $pronoun . ' attorney-in-fact ', $this->normal());
      $textRun->addText($aif->name, $this->bold());
    } else {
      $textRun->addText('"', $this->normal());
    }

    $textRun->addText(';', $this->normal());

    return $textRun;
  }

  // ---------------------------------------------------------------
  // JESSA MONCADA GARGALICANO HENWOOD, of legal age, Filipino,
  // married to CHRIS B E HENWOOD, and a resident of Bacolod City,
  // Negros Occidental, Philippines, hereinafter referred as the
  // "VENDEE" represented by her attorney-in-fact JOEMAR A. MAGBANUA;
  // ---------------------------------------------------------------
  private function singular(DeedOfAbsoluteSaleDocumentPartyMember $member, ?DeedOfAbsoluteSaleDocumentPartyMember $aif = null): TextRun
  {
    $textRun = new TextRun();
    $textRun->addText("\t");
    $pronoun = $this->pronoun($member);
    $label   = $this->label();

    $textRun->addText(strtoupper($member->name), $this->bold());
    $textRun->addText(', of legal age, Filipino', $this->normal());

    if ($member->is_married && $member->spouse) {
      $textRun->addText(', married to ', $this->normal());
      $textRun->addText(strtoupper($member->spouse->name), $this->bold());
    } else {
      $textRun->addText(', single', $this->normal());
    }
    $textRun->addText(', and a resident of ', $this->normal());
    $textRun->addText($this->residence($member), $this->normal());
    $textRun->addText(', Philippines, hereinafter referred as the "', $this->normal());
    $textRun->addText($label, $this->bold());

    if ($aif) {
      $textRun->addText(
        '" represented by ' . $pronoun . ' attorney-in-fact ',
        $this->normal()
      );
      $textRun->addText(strtoupper($aif->name), $this->bold());
    } else {
      $textRun->addText('"', $this->normal());
    }

    $textRun->addText(';', $this->normal());

    return $textRun;
  }

  // ---------------------------------------------------------------
  // Helpers
  // ---------------------------------------------------------------

  private function joinNames(Collection $members): string
  {
    $names = $members->map(fn(DeedOfAbsoluteSaleDocumentPartyMember $m) => $m->name);

    return match($names->count()) {
      2       => $names->join(' and '),
      default => $names->slice(0, -1)->join(', ')
        . ', and '
        . $names->last(),
    };
  }

  private function pronoun(DeedOfAbsoluteSaleDocumentPartyMember $person): string
  {
    return match($person->gender) {
      'female' => 'her',
      'male'   => 'his',
      default  => 'their',
    };
  }

  private function groupPronoun(Collection $members): string
  {
    $genders = $members->pluck('gender')->unique();

    return $genders->count() === 1
      ? $this->pronoun($members->first())
      : 'their';
  }

  private function commonResidence(Collection $members): string
  {
    $residences = $members
      ->map(fn (DeedOfAbsoluteSaleDocumentPartyMember $m) => $this->residence($m))
      ->unique()
      ->values();

    return $residences->count() === 1
      ? $residences->first()
      : $residences->join(' and ');
  }

  // helper to format residence with proper spacing and capitalization
  private function residence(
    DeedOfAbsoluteSaleDocumentPartyMember $member
  ): string {
    return collect([
      $member->city,
      $member->province,
    ])
      ->filter()
      ->join(', ');
  }
}
