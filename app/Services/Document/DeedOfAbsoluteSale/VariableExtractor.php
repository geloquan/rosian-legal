<?php

namespace App\Services\Document\DeedOfAbsoluteSale;

use App\Documents\DeedOfAbsoluteSale\VendeeProseBuilder;
use App\Documents\DeedOfAbsoluteSale\VendorProseBuilder;
use App\Models\DeedOfAbsoluteSaleDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class VariableExtractor
{
  public function extract(DeedOfAbsoluteSaleDocument $deed): array
  {
    $members = $deed->partyMembers;

    Log::info('Extracting variables for deed ID: ' . $deed->uuid);
    Log::info('Total party members found: ' . $members->count());

    $vendeeMembers = $members->filter(fn ($member) => in_array(
      $member->role->value,
      [
        'principal-vendee',
        'principal-vendee-husband',
        'principal-vendee-wife',
      ]
    ));

    $vendorMembers = $members->filter(fn ($member) => in_array(
      $member->role->value,
      [
        'principal-vendor',
        'principal-vendor-husband',
        'principal-vendor-wife',
      ]
    ));

    $vendeeSpouse = $vendeeMembers->firstWhere('role', 'principal-vendee-wife')
      ?? $vendeeMembers->firstWhere('role', 'principal-vendee-husband');

    $vendorSpouse = $vendorMembers->firstWhere('role', 'principal-vendor-wife')
      ?? $vendorMembers->firstWhere('role', 'principal-vendor-husband');

    $aifVendee = $members->firstWhere('role', 'vendee-attorney-in-fact');
    $aifVendor = $members->firstWhere('role', 'vendor-attorney-in-fact');

    $vendeeMembers->each(function ($member) {
      $member->name = Str::upper($member->name);
    });

    $vendorMembers->each(function ($member) {
      $member->name = Str::upper($member->name);
    });

    if ($aifVendee) {
      $aifVendee->name = Str::upper($aifVendee->name);
    }

    if ($aifVendor) {
      $aifVendor->name = Str::upper($aifVendor->name);
    }

    Log::info('Extracting variables for deed ID: ' . $deed->uuid);
    Log::info('Vendee Members: ' . $vendeeMembers->pluck('name')->join(', '));
    Log::info('Vendor Members: ' . $vendorMembers->pluck('name')->join(', '));
    Log::info('Vendee Spouse: ' . ($vendeeSpouse ? $vendeeSpouse->name : 'None'));
    Log::info('Vendor Spouse: ' . ($vendorSpouse ? $vendorSpouse->name : 'None'));
    Log::info('Vendee Attorney-in-Fact: ' . ($vendeeMembers->firstWhere('role', 'vendee-attorney-in-fact') ? $vendeeMembers->firstWhere('role', 'vendee-attorney-in-fact')->name : 'None'));
    Log::info('Vendor Attorney-in-Fact: ' . ($vendorMembers->firstWhere('role', 'vendor-attorney-in-fact') ? $vendorMembers->firstWhere('role', 'vendor-attorney-in-fact')->name : 'None'));

    return [
      'vendee' => (new VendeeProseBuilder)->build(
        $vendeeMembers,
        $aifVendee,
      ),

      'vendor' => (new VendorProseBuilder)->build(
        $vendorMembers,
        $aifVendor,
      ),
//
//      'execution_date' => $deed->executed_at->format('F d, Y'),
//      'property_location' => $deed->property_location,
//      'purchase_price' => number_format($deed->purchase_price, 2),
    ];
  }
}
