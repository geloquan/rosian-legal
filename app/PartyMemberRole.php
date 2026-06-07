<?php

namespace App;

enum PartyMemberRole: string
{
  case AttorneyInFact = 'attorney-in-fact';
  case PrincipalVendor = 'principal-vendor';
  case PrincipalVendee = 'principal-vendee';
  case VendorAttorneyInFact = 'vendor-attorney-in-fact';
  case VendeeAttorneyInFact = 'vendee-attorney-in-fact';
  case PrincipalVendorHusband = 'principal-vendor-husband';
  case PrincipalVendorWife = 'principal-vendor-wife';
  case PrincipalVendeeHusband = 'principal-vendee-husband';
  case PrincipalVendeeWife = 'principal-vendee-wife';
//  public function label(): string
//  {
//    return match ($this) {
//      self::AttorneyInFact => 'Attorney-in-Fact',
//      self::PrincipalVendor => 'Principal Vendor',
//      self::PrincipalVendee => 'Principal Vendee',
//      self::VendorAttorneyInFact => 'Vendor Attorney-in-Fact',
//      self::VendeeAttorneyInFact => 'Vendee Attorney-in-Fact',
//      self::PrincipalVendorHusband => 'Principal Vendor Husband',
//      self::PrincipalVendorWife => 'Principal Vendor Wife',
//      self::PrincipalVendeeHusband => 'Principal Vendee Husband',
//      self::PrincipalVendeeWife => 'Principal Vendee Wife',
//    };
//  }
}
