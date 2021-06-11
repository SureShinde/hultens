<?php

namespace Proclient\PyramidAPI\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class SoapSenderObject extends AbstractHelper {

  function newKundinformation($kund, $forsaljning) {
    $obj = new \stdClass;
    $obj->kund = $kund;
    $obj->forsaljning = $forsaljning;
    return $obj;
  }

  function newKund() {
    $obj = new \stdClass;
    $obj->foretagskod = null;
    $obj->webbforetagskod = null;
    $obj->foretag = null;
    $obj->gatuadress = null;
    $obj->gatupostadress = null;
    $obj->extra_adress = null;
    $obj->landkod = null;
    $obj->telefon = null;
    $obj->e_postadress = null;
    $obj->kundtyp = null;
    $obj->kundstatus = 'OK';
    $obj->kundkategori = null;
    return $obj;
  }

  function newForsaljning() {
    $obj = new \stdClass;
    $obj->projekttyp_kund = null;
    return $obj;
  }

  function newOrderhuvud() {
    $obj = new \stdClass;
    $obj->ordernr = null;
    $obj->foretagskod = null;
    $obj->webbforetagskod = null;
    $obj->webbordernr = null;
    $obj->kontaktnr = null;
    $obj->lager = null;
    $obj->projekttyp = null;
    $obj->orderdatum = null;
    $obj->saljare = null;
    $obj->er_referens = null;
    $obj->ert_ordernr = null;
    $obj->onskat_levdatum = null;
    $obj->forsvillkor = null;
    $obj->leveransadress = null;
    $obj->ovrigt = null;
    return $obj;
  }

  function newForsvillkor() {
    $obj = new \stdClass;
    $this->transportsatt = null;
    $this->betalningsvillkor = null;
    $this->fraktavgift = null;
    $this->expeditionsavgift = null;
    return $obj;
  }

  function newLeveransadress() {
    $obj = new \stdClass;
    $obj->adresskod = null;
    $obj->leveransadress_1 = null;
    $obj->leveransadress_2 = null;
    $obj->leveransadress_3 = null;
    $obj->leveransadress_4 = null;
    $obj->leveransadress_5 = null;
    $obj->leveransadress_6 = null;
    $obj->godsmarkning = null;
    $obj->landkod = null;
    return $obj;
  }

  function newOvrigt() {
    $obj = new \stdClass;
    $obj->valutakod = null;
    return $obj;
  }

  function newOrderrad() {
    $obj = new \stdClass;
    $obj->radnr = null;
    $obj->artikelkod = null;
    $obj->ean_kod = null;
    $obj->benamning_1 = null;
    $obj->budget_antal = null;
    $obj->normalpris = null;
    $obj->normalpris_inkl_moms = null;
    $obj->momsbelopp = null;
    $obj->rabat_procent = null;
    $obj->belopp = null;
    $obj->belopp_inkl_moms = null;
    $obj->onskat_levdatum = null;
    return $obj;
  }

  function newTextrad() {
    $obj = new \stdClass;
    $obj->radnr = null;
    $obj->fri_text = null;
    $obj->blankett_typer = null;
    return $obj;
  }
}
