<?php

namespace App\Services\Document\DeedOfAbsoluteSale;

use App\Documents\DeedOfAbsoluteSale\VendeeProseBuilder;
use App\Documents\DeedOfAbsoluteSale\VendorProseBuilder;
use App\Models\DeedOfAbsoluteSaleDocument;
use Illuminate\Support\Facades\Log;

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
        'vendee-attorney-in-fact',
      ]
    ));

    $vendorMembers = $members->filter(fn ($member) => in_array(
      $member->role->value,
      [
        'principal-vendor',
        'principal-vendor-husband',
        'principal-vendor-wife',
        'vendor-attorney-in-fact',
      ]
    ));

    $vendeeSpouse = $vendeeMembers->firstWhere('role', 'principal-vendee-wife')
      ?? $vendeeMembers->firstWhere('role', 'principal-vendee-husband');

    $vendorSpouse = $vendorMembers->firstWhere('role', 'principal-vendor-wife')
      ?? $vendorMembers->firstWhere('role', 'principal-vendor-husband');

    Log::info('Extracting variables for deed ID: ' . $deed->uuid);
    Log::info('Vendee Members: ' . $vendeeMembers->pluck('name')->join(', '));
    Log::info('Vendor Members: ' . $vendorMembers->pluck('name')->join(', '));
    Log::info('Vendee Spouse: ' . ($vendeeSpouse ? $vendeeSpouse->name : 'None'));
    Log::info('Vendor Spouse: ' . ($vendorSpouse ? $vendorSpouse->name : 'None'));

    return [
      'vendee' => (new VendeeProseBuilder)->build(
        $vendeeMembers,
        $vendeeSpouse,
      ),

      'vendor' => (new VendorProseBuilder)->build(
        $vendorMembers,
        $vendorSpouse,
      ),
//
//      'execution_date' => $deed->executed_at->format('F d, Y'),
//      'property_location' => $deed->property_location,
//      'purchase_price' => number_format($deed->purchase_price, 2),
    ];
  }
}
