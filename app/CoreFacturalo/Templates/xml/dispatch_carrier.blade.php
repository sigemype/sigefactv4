{!! '<?xml version="1.0" encoding="utf-8" standalone="no"?>' !!}
<DespatchAdvice xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"
                xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"
                xmlns:ds="http://www.w3.org/2000/09/xmldsig#"
                xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"
                xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent/>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>2.0</cbc:CustomizationID>
    <cbc:ID>{{ $document['series'] }}-{{ $document['number'] }}</cbc:ID>
    <cbc:IssueDate>{{ $document['date_of_issue'] }}</cbc:IssueDate>
    <cbc:IssueTime>{{ $document['time_of_issue'] }}</cbc:IssueTime>
    <cbc:DespatchAdviceTypeCode listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01"
                                listName="Tipo de Documento"
                                listAgencyName="PE:SUNAT">{{ $document['document_type_id'] }}</cbc:DespatchAdviceTypeCode>
    @if($document['observations'])
        <cbc:Note><![CDATA[{{ $document['observations'] }}]]></cbc:Note>
    @endif
    <cac:Signature>
        <cbc:ID>{{ config('configuration.signature_uri') }}</cbc:ID>
        <cbc:Note>{{ config('configuration.signature_note') }}</cbc:Note>
        <cac:SignatoryParty>
            <cac:PartyIdentification>
                <cbc:ID>{{ $document['company_number'] }}</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyName>
                <cbc:Name><![CDATA[{{ $document['company_name'] }}]]></cbc:Name>
            </cac:PartyName>
        </cac:SignatoryParty>
        <cac:DigitalSignatureAttachment>
            <cac:ExternalReference>
                <cbc:URI>#{{ config('configuration.signature_uri') }}</cbc:URI>
            </cac:ExternalReference>
        </cac:DigitalSignatureAttachment>
    </cac:Signature>
    <!-- DATOS DEL EMISOR (TRANSPORTISTA) -->
    <cac:DespatchSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"
                        schemeAgencyName="PE:SUNAT"
                        schemeName="Documento de Identidad"
                        schemeID="6">{{ $document['company_number'] }}</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[{{ $document['company_name'] }}]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:DespatchSupplierParty>
    <!-- DATOS DEL RECEPTOR (DESTINATARIO) -->
    <cac:DeliveryCustomerParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"
                        schemeAgencyName="PE:SUNAT"
                        schemeName="Documento de Identidad"
                        schemeID="{{ $document['receiver_identity_document_type_id'] }}">{{ $document['receiver_number'] }}</cbc:ID>
            </cac:PartyIdentification>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[{{ $document['receiver_name'] }}]]></cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:DeliveryCustomerParty>
    <!-- DATOS DE QUIEN PAGA EL SERVICIO -->
{{--    <cac:OriginatorCustomerParty>--}}
{{--        <cac:Party>--}}
{{--            <cac:PartyIdentification>--}}
{{--                <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"--}}
{{--                        schemeAgencyName="PE:SUNAT"--}}
{{--                        schemeName="Documento de Identidad"--}}
{{--                        schemeID="6">10417844398</cbc:ID>--}}
{{--            </cac:PartyIdentification>--}}
{{--            <cac:PartyLegalEntity>--}}
{{--                <cbc:RegistrationName>ERIQUE GASPAR CARLOS ALFREDO</cbc:RegistrationName>--}}
{{--            </cac:PartyLegalEntity>--}}
{{--        </cac:Party>--}}
{{--    </cac:OriginatorCustomerParty>--}}
    <cac:Shipment>
        <!-- ID OBLIGATORIO POR UBL -->
        <cbc:ID>1</cbc:ID>
        <!-- PESO BRUTO TOTAL DE LA CARGA-->
        <cbc:GrossWeightMeasure
            unitCode="{{ $document['unit_type_id'] }}">{{ $document['total_weight'] }}</cbc:GrossWeightMeasure>
        <cac:ShipmentStage>
            <!-- FECHA DE INICIO DEL TRASLADO -->
            <cac:TransitPeriod>
                <cbc:StartDate>{{ $document['date_of_shipping'] }}</cbc:StartDate>
            </cac:TransitPeriod>
            <!-- CONDUCTOR PRINCIPAL -->
                <cac:DriverPerson>
                    <!-- TIPO Y NUMERO DE DOCUMENTO DE IDENTIDAD -->
                    <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"
                            schemeAgencyName="PE:SUNAT"
                            schemeName="Documento de Identidad"
                            schemeID="{{ $document['driver_identity_document_type_id'] }}">{{ $document['driver_number'] }}</cbc:ID>
                    <!-- NOMBRES -->
                    <cbc:FirstName>{{ $document['driver_names'] }}</cbc:FirstName>
                    <!-- APELLIDOS -->
                    <cbc:FamilyName>{{ $document['driver_lastnames'] }}</cbc:FamilyName>
                    <!-- TIPO DE CONDUCTOR: PRINCIPAL -->
                    <cbc:JobTitle>Principal</cbc:JobTitle>
                    <cac:IdentityDocumentReference>
                        <!-- LICENCIA DE CONDUCIR -->
                        <cbc:ID>{{ $document['driver_license'] }}</cbc:ID>
                    </cac:IdentityDocumentReference>
                </cac:DriverPerson>
        </cac:ShipmentStage>
        <cac:Delivery>
            <!-- DIRECCION DEL PUNTO DE LLEGADA -->
            <cac:DeliveryAddress>
                <!-- UBIGEO DE LLEGADA -->
                <cbc:ID schemeAgencyName="PE:INEI"
                        schemeName="Ubigeos">{{ $document['receiver_address_location_id'] }}</cbc:ID>
                <cac:AddressLine>
                    <cbc:Line><![CDATA[{{ $document['receiver_address_address'] }}]]></cbc:Line>
                </cac:AddressLine>
            </cac:DeliveryAddress>
            <cac:Despatch>
                <!-- DIRECCION DEL PUNTO DE PARTIDA -->
                <cac:DespatchAddress>
                    <!-- UBIGEO DE PARTIDA -->
                    <cbc:ID schemeAgencyName="PE:INEI"
                            schemeName="Ubigeos">{{ $document['sender_address_location_id'] }}</cbc:ID>
                    <!-- DIRECCION COMPLETA Y DETALLADA DE PARTIDA -->
                    <cac:AddressLine>
                        <cbc:Line><![CDATA[{{ $document['sender_address_address'] }}]]></cbc:Line>
                    </cac:AddressLine>
                </cac:DespatchAddress>
                <!-- DATOS DEL REMITENTE -->
                <cac:DespatchParty>
                    <cac:PartyIdentification>
                        <cbc:ID schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06"
                                schemeAgencyName="PE:SUNAT"
                                schemeName="Documento de Identidad"
                                schemeID="{{ $document['sender_identity_document_type_id'] }}">{{ $document['sender_number'] }}</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyLegalEntity>
                        <cbc:RegistrationName><![CDATA[{{ $document['sender_name'] }}]]></cbc:RegistrationName>
                    </cac:PartyLegalEntity>
                </cac:DespatchParty>
            </cac:Despatch>
        </cac:Delivery>
        <cac:TransportHandlingUnit>
            <!-- NUMERO DE CONTENEDOR -->
            <cbc:ID>-</cbc:ID>
            <cac:TransportEquipment>
                <!-- VEHICULO PRINCIPAL -->
                <!-- PLACA - VEHICULO PRINCIPAL -->
                <cbc:ID>{{ $document['transport_plate_number'] }}</cbc:ID>
            </cac:TransportEquipment>
        </cac:TransportHandlingUnit>
    </cac:Shipment>
    <!-- DETALLES DE BIENES A TRASLADAR -->
    @foreach($document['items'] as $row)
        <cac:DespatchLine>
            <!-- NUMERO DE ORDEN DEL ITEM -->
            <cbc:ID>{{ $loop->iteration }}</cbc:ID>
            <!-- CANTIDAD -->
            <cbc:DeliveredQuantity unitCode="{{ $row['unit_type_id'] }}"
                                   unitCodeListAgencyName="United Nations Economic Commission for Europe"
                                   unitCodeListID="UN/ECE rec 20">{{ $row['quantity'] }}</cbc:DeliveredQuantity>
            <cac:OrderLineReference>
                <cbc:LineID>{{ $loop->iteration }}</cbc:LineID>
            </cac:OrderLineReference>
            <cac:Item>
                <cbc:Description><![CDATA[{{ $row['name'] }}]]></cbc:Description>
                <cac:SellersItemIdentification>
                    <cbc:ID><![CDATA[{{ $row['internal_id'] }}]]></cbc:ID>
                </cac:SellersItemIdentification>
            </cac:Item>
        </cac:DespatchLine>
    @endforeach
</DespatchAdvice>
