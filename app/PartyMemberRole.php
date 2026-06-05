<?php

namespace App;

enum PartyMemberRole: string
{
  case Vendor = 'vendor';
  case Vendee = 'vendee';
  case AttorneyInFact = 'attorney-in-fact';
  case PrincipalVendor = 'principal-vendor';
  case PrincipalVendee = 'principal-vendee';
}
