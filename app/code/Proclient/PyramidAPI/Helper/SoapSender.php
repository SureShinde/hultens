<?php

namespace Proclient\PyramidAPI\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class SoapSender extends AbstractHelper {

  public function getCustomer($url, $username, $password, $kundinformation, $debug, $logger) {
    $xml_kundinformation = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <kundinformation xmlns="'.$url.'">
          <meta>
            <version>1.00.00</version>
            <kundunik></kundunik>
            <tidsstampel></tidsstampel>
            <funktion>info</funktion>
            <utokad>N</utokad>
          </meta>
          <kunddata>
            <kund>
              <foretagskod>'.$kundinformation->kund->foretagskod.'</foretagskod>
              <foretag></foretag>
            </kund>
          </kunddata>
        </kundinformation >
      </soap:Body>
    </soap:Envelope>';

    $headers = array(
      "Content-type: text/xml;charset=\"utf-8\"",
      "Accept: text/xml",
      "Cache-Control: no-cache",
      "Pragma: no-cache",
      "SOAPAction: kundinformation",
      "Content-length: ".strlen($xml_kundinformation)
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_kundinformation);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (strlen($username) and strlen($password)) {
      curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    }
    $response = curl_exec($ch);
    curl_close($ch);

    if (strpos($response, '<status>OK') !== false) {
      $response1 = str_replace("<soap:Body>","",$response);
      $response2 = str_replace("</soap:Body>","",$response1);
      $parser = simplexml_load_string($response2);
      if (isset($parser->kundinformation) and isset($parser->kundinformation->kunddata) and isset($parser->kundinformation->kunddata->kund) and isset($parser->kundinformation->kunddata->kund->foretagskod)) {
        return $parser->kundinformation->kunddata->kund->foretagskod;
      }
      return 'ERROR';
    } else if (strpos($response,'faultstring') !== false) {
      $response = explode('faultstring',$response);
      $response = str_replace('>', '', $response[1]);
      $response = str_replace('</', '', $response);
      if ($response == 'Kund saknas') {
        return 'NOT_FOUND';
      } else {
        return 'ERROR';
      }
    } else {
      return 'ERROR';
    }
  }

  public function getCustomers($url, $username, $password) {
    $xml_artikelinformation = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <hamtaKunder xmlns="'.$url.'">
          <meta>
            <version>1.00.00</version>
            <kundunik>N</kundunik>
            <tidstampel></tidstampel>
            <utokad>J</utokad>
          </meta>
        </hamtaKunder >
      </soap:Body>
    </soap:Envelope>';

    $headers = array(
      "Content-type: text/xml;charset=\"utf-8\"",
      "Accept: text/xml",
      "Cache-Control: no-cache",
      "Pragma: no-cache",
      "SOAPAction: hamtaKunder",
      "Content-length: ".strlen($xml_artikelinformation),
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_artikelinformation);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 400);
    if (strlen($username) and strlen($password)) {
      curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    }
    $response = curl_exec($ch);
    curl_close($ch);
    $response1 = str_replace("<soap:Body>","",$response);
    $response2 = str_replace("</soap:Body>","",$response1);
    $parser = simplexml_load_string($response2);
    if (isset($parser->hamtaKunder) and isset($parser->hamtaKunder->kunder)) {
      return $parser->hamtaKunder->kunder;
    }
    return false;

  }

	public function createCustomer($url, $username, $password, $kundinformation, $debug, $logger) {
    $xml_kundinformation = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <kundinformation xmlns="'.$url.'">
          <meta>
            <version>1.00.00</version>
            <kundunik>N</kundunik>
            <tidsstampel></tidsstampel>
            <funktion>create</funktion>
            <utokad>J</utokad>
          </meta>
          <kunddata>
            <kund>
              <foretagskod>'.$kundinformation->kund->foretagskod.'</foretagskod>
              <webbforetagskod>'.$kundinformation->kund->webbforetagskod.'</webbforetagskod>
              <foretag>'.$this->replace_chars($kundinformation->kund->foretag).'</foretag>
              <gatuadress>'.$this->replace_chars($kundinformation->kund->gatuadress).'</gatuadress>
              <gatupostadress>'.$this->replace_chars($kundinformation->kund->gatupostadress).'</gatupostadress>
              <extra_adress>'.$this->replace_chars($kundinformation->kund->extra_adress).'</extra_adress>
              <landkod>'.$kundinformation->kund->landkod.'</landkod>
              <telefon>'.$kundinformation->kund->telefon.'</telefon>
              <mobiltelefon></mobiltelefon>
              <e_postadress>'.$kundinformation->kund->e_postadress.'</e_postadress>
              <hemsida></hemsida>
              <kundtyp>'.$kundinformation->kund->kundtyp.'</kundtyp>
              <kundstatus>'.$kundinformation->kund->kundstatus.'</kundstatus>
              <kundkategori>'.$kundinformation->kund->kundkategori.'</kundkategori>
              <organisationsnr></organisationsnr>
              <vat_nummer></vat_nummer>
            </kund>
            <forsaljning>
              <projekttyp_kund>'.$kundinformation->forsaljning->projekttyp_kund.'</projekttyp_kund>
            </forsaljning>
            <lev_adresser>
              <leveransadress_1></leveransadress_1>
              <leveransadress_2></leveransadress_2>
              <leveransadress_3></leveransadress_3>
              <leveransadress_4></leveransadress_4>
              <leveransadress_5></leveransadress_5>
              <leveransadress_6></leveransadress_6>
              <landkod></landkod>
            </lev_adresser>
          </kunddata>
        </kundinformation >
      </soap:Body>
    </soap:Envelope>';
    $headers = array(
      "Content-type: text/xml;charset=\"utf-8\"",
      "Accept: text/xml",
      "Cache-Control: no-cache",
      "Pragma: no-cache",
      "SOAPAction: kundinformation",
      "Content-length: ".strlen($xml_kundinformation),
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_kundinformation);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (strlen($username) and strlen($password)) {
      curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    }
    $response = curl_exec($ch);
    curl_close($ch);
    if (strpos($response, '<status>OK') !== false) {
      $response1 = str_replace("<soap:Body>","",$response);
      $response2 = str_replace("</soap:Body>","",$response1);
      $parser = simplexml_load_string($response2);
      if (isset($parser->kundinformation) and isset($parser->kundinformation->kunddata) and isset($parser->kundinformation->kunddata->kund) and isset($parser->kundinformation->kunddata->kund->foretagskod)) {
        return $parser->kundinformation->kunddata->kund->foretagskod;
      }
      return 'CUSTOMER_CREATED';
    } else if (strpos($response,'faultstring') !== false) {
      $response = explode('faultstring',$response);
      $response = str_replace('>', '', $response[1]);
      $response = str_replace('</', '', $response);
      if ($response == 'Kund finns redan, skicka update istället') {
        return 'ALREADY_CREATED';
      } else {
        return 'ERROR';
      }
    } else {
      return 'ERROR';
    }
	}

  public function updateCustomer($url, $username, $password, $kundinformation, $debug, $logger) {
    $xml_kundinformation = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <kundinformation xmlns="'.$url.'">
          <meta>
            <version>1.00.00</version>
            <kundunik>N</kundunik>
            <tidsstampel></tidsstampel>
            <funktion>update</funktion>
            <utokad>J</utokad>
          </meta>
          <kunddata>
            <kund>
              <foretagskod>'.$kundinformation->kund->foretagskod.'</foretagskod>
              <foretag></foretag>
              <gatuadress>'.$this->replace_chars($kundinformation->kund->gatuadress).'</gatuadress>
              <gatupostadress>'.$this->replace_chars($kundinformation->kund->gatupostadress).'</gatupostadress>
              <extra_adress>'.$this->replace_chars($kundinformation->kund->extra_adress).'</extra_adress>
              <landkod>'.$kundinformation->kund->landkod.'</landkod>
              <telefon>'.$kundinformation->kund->telefon.'</telefon>
              <mobiltelefon></mobiltelefon>
              <e_postadress>'.$kundinformation->kund->e_postadress.'</e_postadress>
              <hemsida></hemsida>
              <kundtyp>'.$kundinformation->kund->kundtyp.'</kundtyp>
              <kundstatus>'.$kundinformation->kund->kundstatus.'</kundstatus>
              <kundkategori>'.$kundinformation->kund->kundkategori.'</kundkategori>
              <organisationsnr></organisationsnr>
              <vat_nummer></vat_nummer>
            </kund>
            <forsaljning>
              <projekttyp_kund></projekttyp_kund>
            </forsaljning>
            <lev_adresser>
              <leveransadress_1></leveransadress_1>
              <leveransadress_2></leveransadress_2>
              <leveransadress_3></leveransadress_3>
              <leveransadress_4></leveransadress_4>
              <leveransadress_5></leveransadress_5>
              <leveransadress_6></leveransadress_6>
              <landkod></landkod>
            </lev_adresser>
          </kunddata>
        </kundinformation >
      </soap:Body>
    </soap:Envelope>';
    $headers = array(
      "Content-type: text/xml;charset=\"utf-8\"",
      "Accept: text/xml",
      "Cache-Control: no-cache",
      "Pragma: no-cache",
      "SOAPAction: kundinformation",
      "Content-length: ".strlen($xml_kundinformation),
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_kundinformation);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (strlen($username) and strlen($password)) {
      curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    }
    $response = curl_exec($ch);
    curl_close($ch);
    if (strpos($response, '<status>OK') !== false) {
      $response1 = str_replace("<soap:Body>","",$response);
      $response2 = str_replace("</soap:Body>","",$response1);
      $parser = simplexml_load_string($response2);
      if (isset($parser->kundinformation) and isset($parser->kundinformation->kunddata) and isset($parser->kundinformation->kunddata->kund) and isset($parser->kundinformation->kunddata->kund->foretagskod)) {
        return $parser->kundinformation->kunddata->kund->foretagskod;
      }
      return 'CUSTOMER_UPDATED';
    } else if (strpos($response,'faultstring') !== false) {
      $response = explode('faultstring',$response);
      $response = str_replace('>', '', $response[1]);
      $response = str_replace('</', '', $response);
      if ($response == 'Kund saknas') {
        return 'NOT_FOUND';
      } else {
        return 'ERROR';
      }
    } else {
      return 'ERROR';
    }
  }

  public function getOrder($url, $username, $password, $orderhuvud, $debug, $logger) {
    $xml_orderinformation = '<?xml version="1.0" encoding="utf-8"?>
      <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
          <orderinformation xmlns="'.$url.'">
            <meta>
              <version>1.00.00</version>
              <kundunik></kundunik>
              <tidsstampel></tidsstampel>
              <funktion>info</funktion>
              <utokad>J</utokad>
            </meta>
            <order>
              <orderhuvud>
                <ordernr>'.$orderhuvud->ordernr.'</ordernr>
                <webbordernr>'.$orderhuvud->webbordernr.'</webbordernr>
                <foretagskod></foretagskod>
                <webbforetagskod></webbforetagskod>
                <kontaktnr></kontaktnr>
                <webbkontaktnr></webbkontaktnr>
                <lager></lager>
              </orderhuvud>
            </order>
          </orderinformation >
        </soap:Body>
      </soap:Envelope>';

    $headers = array(
      "Content-type: text/xml;charset=\"utf-8\"",
      "Accept: text/xml",
      "Cache-Control: no-cache",
      "Pragma: no-cache",
      "SOAPAction: orderinformation",
      "Content-length: ".strlen($xml_orderinformation),
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_orderinformation);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (strlen($username) and strlen($password)) {
      curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    }
    $response = curl_exec($ch);
    curl_close($ch);
    if (strpos($response, '<status>OK') !== false) {
      $response1 = str_replace("<soap:Body>","",$response);
      $response2 = str_replace("</soap:Body>","",$response1);
      $parser = simplexml_load_string($response2);
      if (isset($parser->orderinformation) and isset($parser->orderinformation->order)) {
        return $parser->orderinformation->order;
      }
      return 'ERROR';
    } else if (strpos($response,'faultstring') !== false) {
      $response = explode('faultstring',$response);
      $response = str_replace('>', '', $response[1]);
      $response = str_replace('</', '', $response);
      if ($response == 'Ordernr saknas') {
        return 'NOT_FOUND';
      } else {
        return 'ERROR';
      }
    } else {
      return 'ERROR';
    }
  }

  public function createOrder($url, $username, $password, $orderhuvud, $orderrader, $textrader, $debug, $logger) {
    $kundunikt = '';
    if (isset($orderhuvud->kundunikt) and is_array($orderhuvud->kundunikt)) {
      foreach ($orderhuvud->kundunikt as $field => $value) {
        $kundunikt .= '<'.$field.'>'.$value.'</'.$field.'>';
      }
    }
    $xml_orderinformation = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <orderinformation xmlns="'.$url.'">
          <meta>
            <version>1.00.00</version>
            <kundunik>N</kundunik>
            <tidstampel></tidstampel>
            <funktion>create</funktion>
            <utokad>J</utokad>
          </meta>
          <order>
            <orderhuvud>
              <foretagskod>'.$orderhuvud->foretagskod.'</foretagskod>
              <webbforetagskod>'.$orderhuvud->webbforetagskod.'</webbforetagskod>
              <webbordernr>'.$orderhuvud->webbordernr.'</webbordernr>
              <kontaktnr>'.$orderhuvud->kontaktnr.'</kontaktnr>
              <webbkontaktnr></webbkontaktnr>
              <lager>'.$orderhuvud->lager.'</lager>
              <projekttyp>'.$orderhuvud->projekttyp.'</projekttyp>
              <orderdatum>'.$orderhuvud->orderdatum.'</orderdatum>
              <saljare>'.$orderhuvud->saljare.'</saljare>
              <er_referens>'.$this->replace_chars($orderhuvud->er_referens).'</er_referens>
              <ert_ordernr>'.$orderhuvud->ert_ordernr.'</ert_ordernr>
              <onskat_levdatum>'.$orderhuvud->onskat_levdatum.'</onskat_levdatum>
              <forsvillkor>
                <transportsatt>'.$orderhuvud->forsvillkor->transportsatt.'</transportsatt>
    						<betalningsvillkor>'.$orderhuvud->forsvillkor->betalningsvillkor.'</betalningsvillkor>
    						<fraktavgift>'.$orderhuvud->forsvillkor->fraktavgift.'</fraktavgift>
    						<expeditionsavgift>'.$orderhuvud->forsvillkor->expeditionsavgift.'</expeditionsavgift>
              </forsvillkor>
              <leveransadress>
                <adresskod>'.$orderhuvud->leveransadress->adresskod.'</adresskod>
                <leveransadress_1>'.$this->replace_chars($orderhuvud->leveransadress->leveransadress_1).'</leveransadress_1>
                <leveransadress_2>'.$this->replace_chars($orderhuvud->leveransadress->leveransadress_2).'</leveransadress_2>
                <leveransadress_3>'.$this->replace_chars($orderhuvud->leveransadress->leveransadress_3).'</leveransadress_3>
                <leveransadress_4>'.$this->replace_chars($orderhuvud->leveransadress->leveransadress_4).'</leveransadress_4>
                <leveransadress_5></leveransadress_5>
                <leveransadress_6></leveransadress_6>
                <godsmarkning>'.$this->replace_chars($orderhuvud->leveransadress->godsmarkning).'</godsmarkning>
                <landkod>'.$orderhuvud->leveransadress->landkod.'</landkod>
              </leveransadress>
              <ovrigt>
    						<forskottsbetalt_belopp />
    						<forskottsbetalt_referens />
    						<valutakod>'.$orderhuvud->ovrigt->valutakod.'</valutakod>
        			</ovrigt>
              '.$kundunikt.'
            </orderhuvud>
            <orderrader>';
            foreach ($orderrader as $orderrad) {
              $xml_orderinformation .= '<artikelrad>
                <radnr>'.$orderrad->radnr.'</radnr>
                <artikelkod>'.$orderrad->artikelkod.'</artikelkod>
                <ean_kod></ean_kod>
                <benamning_1>'.$this->replace_chars($orderrad->benamning_1).'</benamning_1>
                <benamning_2></benamning_2>
                <budget_antal>'.$orderrad->budget_antal.'</budget_antal>
                <enhet></enhet>
                <normalpris>'.$orderrad->normalpris.'</normalpris>
                <normalpris_inkl_moms>'.$orderrad->normalpris_inkl_moms.'</normalpris_inkl_moms>
                <momsbelopp>'.$orderrad->momsbelopp.'</momsbelopp>
                <rabat_procent>'.$orderrad->rabat_procent.'</rabat_procent>
                <belopp>'.$orderrad->belopp.'</belopp>
                <belopp_inkl_moms>'.$orderrad->belopp_inkl_moms.'</belopp_inkl_moms>
                <onskat_levdatum>'.$orderrad->onskat_levdatum.'</onskat_levdatum>
              </artikelrad>';
            }
            foreach ($textrader as $textrad) {
              $xml_orderinformation .= '<textrad>
  				      <radnr>'.$textrad->radnr.'</radnr>
  				      <fri_text>'.$this->replace_chars($textrad->fri_text).'</fri_text>
  				      <blankett_typer />
  			      </textrad>';
            }
          $xml_orderinformation .= '</orderrader>
          </order>
        </orderinformation>
      </soap:Body>
    </soap:Envelope>';
    $headers = array(
      "Content-type: text/xml;charset=\"utf-8\"",
      "Accept: text/xml",
      "Cache-Control: no-cache",
      "Pragma: no-cache",
      "SOAPAction: orderinformation",
      "Content-length: ".strlen($xml_orderinformation),
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_orderinformation);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (strlen($username) and strlen($password)) {
      curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    }
    $response = curl_exec($ch);

    curl_close($ch);
    if (strpos($response, '<status>OK') !== false) {
      if ($debug)
        $logger->info('Proclient\PyramidAPI\Helper\SoapSender\createOrder got status OK');
      $response1 = str_replace("<soap:Body>","",$response);
      $response2 = str_replace("</soap:Body>","",$response1);
      $parser = simplexml_load_string($response2);
      if (isset($parser->orderinformation) and isset($parser->orderinformation->order) and isset($parser->orderinformation->order->orderhuvud) and isset($parser->orderinformation->order->orderhuvud->ordernr)) {
        return $parser->orderinformation->order->orderhuvud->ordernr;
      }
      return 'ORDER_CREATED';
    } else if (strpos($response,'faultstring') !== false) {
      $response = explode('faultstring',$response);
      $response = str_replace('>', '', $response[1]);
      $response = str_replace('</', '', $response);
      if ($debug)
        $logger->info('Proclient\PyramidAPI\Helper\SoapSender\createOrder got a faultstring: '.$response);
      if ($response == 'Ordern finns redan, skicka update istället') {
        return 'ALREADY_CREATED';
      } elseif ($response == 'Artikel saknas. Enligt egenskap tillåts inte skapa artiklar.') {
        return 'MISSING_ARTICLE';
      }
      return 'ERROR';
    }
    if ($debug)
      $logger->info('Proclient\PyramidAPI\Helper\SoapSender\createOrder did not get an expected answer: '.$response);
    return 'ERROR';
  }

  public function updateOrder($url, $username, $password, $orderhuvud, $debug, $logger) {
    $xml_orderinformation = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <orderinformation xmlns="'.$url.'">
          <meta>
            <version>1.00.00</version>
            <kundunik>N</kundunik>
            <tidstampel></tidstampel>
            <funktion>update</funktion>
            <utokad>J</utokad>
          </meta>
          <order>
            <orderhuvud>
              <ordernr>'.$orderhuvud->ordernr.'</ordernr>
              <webbordernr>'.$orderhuvud->webbordernr.'</webbordernr>
      				<projektstatus>'.$orderhuvud->projektstatus.'</projektstatus>
            </orderhuvud>
          </order>
        </orderinformation >
      </soap:Body>
    </soap:Envelope>';

    $headers = array(
      "Content-type: text/xml;charset=\"utf-8\"",
      "Accept: text/xml",
      "Cache-Control: no-cache",
      "Pragma: no-cache",
      "SOAPAction: orderinformation",
      "Content-length: ".strlen($xml_orderinformation),
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_orderinformation);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (strlen($username) and strlen($password)) {
      curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    }
    $response = curl_exec($ch);
    curl_close($ch);
    if (strpos($response, '<status>OK') !== false) {
      $response1 = str_replace("<soap:Body>","",$response);
      $response2 = str_replace("</soap:Body>","",$response1);
      $parser = simplexml_load_string($response2);
      if (isset($parser->orderinformation) and isset($parser->orderinformation->order) and isset($parser->orderinformation->order->orderhuvud) and isset($parser->orderinformation->order->orderhuvud->ordernr)) {
        return $parser->orderinformation->order->orderhuvud->ordernr;
      }
      return 'ORDER_UPDATED';
    } else if (strpos($response,'faultstring') !== false) {
      $response = explode('faultstring',$response);
      $response = str_replace('>', '', $response[1]);
      $response = str_replace('</', '', $response);
      if ($response == 'Kund saknas') {
        return 'NOT_FOUND';
      } else {
        return 'ERROR';
      }
    } else {
      return 'ERROR';
    }
  }

  public function getArticles($url, $username, $password) {
   $xml_artikelinformation = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
      <soap:Body>
        <HamtaArtiklarReq xmlns="'.$url.'">
          <meta>
            <version>1.00.00</version>
            <kundunik>N</kundunik>
            <tidstampel></tidstampel>
            <utokad>J</utokad>
          </meta>
        </HamtaArtiklarReq>
      </soap:Body>
    </soap:Envelope>';

    $headers = array(
      "Content-type: text/xml;charset=\"utf-8\"",
      "Accept: text/xml",
      "Cache-Control: no-cache",
      "Pragma: no-cache",
      "SOAPAction: HamtaArtiklarReq",
      "Content-length: ".strlen($xml_artikelinformation),
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_artikelinformation);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 400);
    if (strlen($username) and strlen($password)) {
      curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    }
    $response = curl_exec($ch);
    curl_close($ch);

    $response = str_replace("<soap:Body>","",$response);
    $response = str_replace("</soap:Body>","",$response);
    $response = str_replace("w20:","",$response);
    $parser = simplexml_load_string($response);
    if (isset($parser->HamtaArtiklarRes) and isset($parser->HamtaArtiklarRes->artiklar) and isset($parser->HamtaArtiklarRes->artiklar->e_artikel)) {
      return $parser->HamtaArtiklarRes->artiklar->e_artikel;
    }
    return false;
  }

  private function replace_chars($string) {
    return str_replace('&', '', $string);
  }
}
