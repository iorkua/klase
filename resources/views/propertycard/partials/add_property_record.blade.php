@php
    $is_ai = $is_ai_assistant ?? false;
@endphp

@if(!$is_ai)
<div id="property-form-dialog" class="dialog-overlay hidden" >
    <div class="dialog-content property-form-content">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Add New Property</h2>
            <button id="close-property-form" class="text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
@endif

<form id="property-record-form" action="{{ route('property-records.store') }}" method="POST" x-data="propertyRecordForm()">
    @csrf
    <input type="hidden" name="property_id" id="property_id" value="">
    <input type="hidden" name="action" id="action" value="add">
    <div class="space-y-4 py-2 @if(!$is_ai) max-h-[75vh] overflow-y-auto pr-1 @endif">
        <!-- Top section with two columns -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Left column - Title Type Section -->
            <div class="form-section">
                <h4 class="form-section-title">Property Type Information</h4>
                <div class="space-y-3">
                    <div class="space-y-1">
                        <label class="text-sm">Title Type</label>
                        <div class="flex space-x-4">
                            <div class="flex items-center space-x-1">
                                <input type="radio" id="customary" name="titleType" value="Customary" checked>
                                <label for="customary" class="text-sm">Customary</label>
                            </div>
                            <div class="flex items-center space-x-1">
                                <input type="radio" id="statutory" name="titleType" value="Statutory">
                                <label for="statutory" class="text-sm">Statutory</label>
                            </div>
                        </div>
                    </div>

                    <!-- File Number -->
                   <div class="space-y-1" x-data="{ showManualEntry: false }">
                    <div class="flex items-center justify-between mb-3">
                        <label for="fileno-select" class="block text-sm font-medium text-gray-700">Select File Number</label>
                        <button type="button" @click="showManualEntry = !showManualEntry" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span x-text="showManualEntry ? 'Use Smart Selector' : 'Enter Fileno manually'"></span>
                        </button>
                    </div>
                    
                    <!-- Smart File Number Selector (Default) -->
                    <div x-show="!showManualEntry" x-transition>
                        @include('propertycard.partials.smart_fileno_selector')
                    </div>
                    
                    <!-- Manual File Number Entry -->
                    <div x-show="showManualEntry" x-transition>
                        @include('propertycard.partials.manual_fileno')
                    </div>
                    </div>
                </div>
            </div>
            
            <!-- Right column - Property Description -->
            <div class="form-section">
                <h4 class="form-section-title">Property Description</h4>
                <div class="space-y-3">
                    <!-- House No and Plot No -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="houseNo" class="text-xs text-gray-600">House No</label>
                            <input id="houseNo" name="house_no" x-model="house" type="text" class="form-input text-sm property-input">
                        </div>
                        <div>
                            <label for="plotNo" class="text-xs text-gray-600">Plot No.</label>
                            <input id="plotNo" name="plot_no" x-model="plot" type="text" class="form-input text-sm property-input" placeholder="Enter plot number">
                        </div>
                    </div>
                    <!-- Street Name and District/Neighbourhood -->
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Street Name Component -->
                        <div class="space-y-2">
                            <label for="streetName" class="text-xs text-gray-600">Street Name</label>
                            <select id="streetName" class="form-input text-sm property-input" 
                                    x-model="selectedStreet"
                                    @change="handleStreetChange($event.target.value)"
                                    name="streetName">
                                <option value="" selected>Select Street Name</option>
                                <option value="10TH ST">10TH ST</option>
        <option value="11TH AV">11TH AV</option>
        <option value="11TH ST">11TH ST</option>
        <option value="12TH AV">12TH AV</option>
        <option value="12TH ST">12TH ST</option>
        <option value="13TH AV">13TH AV</option>
        <option value="13TH LINK">13TH LINK</option>
        <option value="13TH ST">13TH ST</option>
        <option value="14TH AV">14TH AV</option>
        <option value="14TH LINK">14TH LINK</option>
        <option value="14TH ST">14TH ST</option>
        <option value="15TH AV">15TH AV</option>
        <option value="15TH LINK">15TH LINK</option>
        <option value="15TH ST">15TH ST</option>
        <option value="16TH AV">16TH AV</option>
        <option value="16TH AV">16TH AV</option>
        <option value="16TH LINK">16TH LINK</option>
        <option value="16TH ST">16TH ST</option>
        <option value="17TH AV">17TH AV</option>
        <option value="17TH LINK">17TH LINK</option>
        <option value="17TH ST">17TH ST</option>
        <option value="18TH LINK">18TH LINK</option>
        <option value="18TH ST">18TH ST</option>
        <option value="19TH AV">19TH AV</option>
        <option value="19TH LINK">19TH LINK</option>
        <option value="19TH ST">19TH ST</option>
        <option value="19TH ST">19TH ST</option>
        <option value="1ST AV">1ST AV</option>
        <option value="1ST GATE">1ST GATE</option>
        <option value="1ST LINK">1ST LINK</option>
        <option value="1ST ST">1ST ST</option>
        <option value="20TH AVENUE">20TH AVENUE</option>
        <option value="20TH ST">20TH ST</option>
        <option value="21ST LINK">21ST LINK</option>
        <option value="21ST ST">21ST ST</option>
        <option value="22ND ST">22ND ST</option>
        <option value="23RD LINK">23RD LINK</option>
        <option value="24TH LINK">24TH LINK</option>
        <option value="25TH LINK">25TH LINK</option>
        <option value="26TH LINK">26TH LINK</option>
        <option value="27TH LINK">27TH LINK</option>
        <option value="28TH LINK">28TH LINK</option>
        <option value="29TH LINK">29TH LINK</option>
        <option value="2ND AV">2ND AV</option>
        <option value="2ND AV">2ND AV</option>
        <option value="2ND GATE">2ND GATE</option>
        <option value="2ND LINK">2ND LINK</option>
        <option value="2ND ST">2ND ST</option>
        <option value="301 RD">301 RD</option>
        <option value="301 RD">301 RD</option>
        <option value="30TH LINK">30TH LINK</option>
        <option value="31ST LINK">31ST LINK</option>
        <option value="32ND LINK">32ND LINK</option>
        <option value="33RD LINK">33RD LINK</option>
        <option value="34TH LINK">34TH LINK</option>
        <option value="37TH LINK">37TH LINK</option>
        <option value="38TH LINK">38TH LINK</option>
        <option value="39TH LINK">39TH LINK</option>
        <option value="3RD AV">3RD AV</option>
        <option value="3RD AV">3RD AV</option>
        <option value="3RD AV">3RD AV</option>
        <option value="3RD AV A CL">3RD AV A CL</option>
        <option value="3RD AV B CL">3RD AV B CL</option>
        <option value="3RD ST">3RD ST</option>
        <option value="3ROAD GATE">3ROAD GATE</option>
        <option value="40TH LINK">40TH LINK</option>
        <option value="4TH ST">4TH ST</option>
        <option value="5TH AV">5TH AV</option>
        <option value="5TH AV 501 RD">5TH AV 501 RD</option>
        <option value="6TH ST">6TH ST</option>
        <option value="7TH AV">7TH AV</option>
        <option value="8TH ST">8TH ST</option>
        <option value="9TH ST">9TH ST</option>
        <option value="A. MAIGORO SAGIGI LINK">A. MAIGORO SAGIGI LINK</option>
        <option value="A.G. BELLO">A.G. BELLO</option>
        <option value="A.I.K ST">A.I.K ST</option>
        <option value="ABA RD">ABA RD</option>
        <option value="ABAGANA ST">ABAGANA ST</option>
        <option value="ABARBA AV">ABARBA AV</option>
        <option value="ABBA ABDULLAHI AV">ABBA ABDULLAHI AV</option>
        <option value="ABBA SAMAILA LINK">ABBA SAMAILA LINK</option>
        <option value="ABBALE ST">ABBALE ST</option>
        <option value="ABBAS ROAD">ABBAS ROAD</option>
        <option value="ABDULLAHI BABANGIDA ST">ABDULLAHI BABANGIDA ST</option>
        <option value="ABDULLAHI BAYERO ROAD">ABDULLAHI BAYERO ROAD</option>
        <option value="ABDULLAHI WASE ROAD">ABDULLAHI WASE ROAD</option>
        <option value="ABDURAZAQ SABO LINK">ABDURAZAQ SABO LINK</option>
        <option value="ABDUULHAMID HASSAN ROAD">ABDUULHAMID HASSAN ROAD</option>
        <option value="ABEOKUTA RD">ABEOKUTA RD</option>
        <option value="ABUBAKAR ALI AV">ABUBAKAR ALI AV</option>
        <option value="ABUBAKAR SADEEQ AV">ABUBAKAR SADEEQ AV</option>
        <option value="ABUJA / FRANCE ROAD">ABUJA / FRANCE ROAD</option>
        <option value="ADAMU DUTSE ST">ADAMU DUTSE ST</option>
        <option value="ADAMU JAOJI RD">ADAMU JAOJI RD</option>
        <option value="ADAMU NAVY AV">ADAMU NAVY AV</option>
        <option value="ADAMU SARKI ST">ADAMU SARKI ST</option>
        <option value="ADMINISTRATOR RD">ADMINISTRATOR RD</option>
        <option value="ADO BAYERO ROAD">ADO BAYERO ROAD</option>
        <option value="ADO GWARAM RD">ADO GWARAM RD</option>
        <option value="ADO MADAKA AV">ADO MADAKA AV</option>
        <option value="ADO MAI NAMA ST">ADO MAI NAMA ST</option>
        <option value="AHAMADIYA ROAD">AHAMADIYA ROAD</option>
        <option value="AHHAD CR">AHHAD CR</option>
        <option value="AHMAD KWATA STREET">AHMAD KWATA STREET</option>
        <option value="AHMADU ASHAKA ST">AHMADU ASHAKA ST</option>
        <option value="AHMADU BELLO WAY">AHMADU BELLO WAY</option>
        <option value="AHMADU MAI ZARA LINE">AHMADU MAI ZARA LINE</option>
        <option value="AHMED DAKU CR">AHMED DAKU CR</option>
        <option value="AHMED NUHU WALI ROAD">AHMED NUHU WALI ROAD</option>
        <option value="AHMED ZAREWA LINE">AHMED ZAREWA LINE</option>
        <option value="AJIYA ST">AJIYA ST</option>
        <option value="AKILU DAMBAZAU RD">AKILU DAMBAZAU RD</option>
        <option value="AKU AV">AKU AV</option>
        <option value="ALARAMMA ST">ALARAMMA ST</option>
        <option value="ALASAN DANTATA STREET">ALASAN DANTATA STREET</option>
        <option value="ALFA WALI AV">ALFA WALI AV</option>
        <option value="ALFADARI ST">ALFADARI ST</option>
        <option value="ALH ABDULLAHI KURAWA ST">ALH ABDULLAHI KURAWA ST</option>
        <option value="ALH DALHA USMAN ST">ALH DALHA USMAN ST</option>
        <option value="ALH. ABUBAKAR TSAV ST">ALH. ABUBAKAR TSAV ST</option>
        <option value="ALH. AWWALU SHEHU ST">ALH. AWWALU SHEHU ST</option>
        <option value="ALH. AYUBA MOHD. ST">ALH. AYUBA MOHD. ST</option>
        <option value="ALH. BATURE ABDULAZIZ ST">ALH. BATURE ABDULAZIZ ST</option>
        <option value="ALH. MUHAMMED DANKAUYE ST">ALH. MUHAMMED DANKAUYE ST</option>
        <option value="ALH. MUSA AGAWA GATE">ALH. MUSA AGAWA GATE</option>
        <option value="ALH. SALLAU LINE">ALH. SALLAU LINE</option>
        <option value="ALH. ZAKARI ST">ALH. ZAKARI ST</option>
        <option value="ALHERI ROAD">ALHERI ROAD</option>
        <option value="ALI AKILU RD">ALI AKILU RD</option>
        <option value="ALI ALHAKIM ROAD">ALI ALHAKIM ROAD</option>
        <option value="ALI MAIKARO ST">ALI MAIKARO ST</option>
        <option value="ALI RANO LINK">ALI RANO LINK</option>
        <option value="ALI ZANGO ST">ALI ZANGO ST</option>
        <option value="ALIYU TSOHON SOJA LINK">ALIYU TSOHON SOJA LINK</option>
        <option value="ALMAJIRAI LINE">ALMAJIRAI LINE</option>
        <option value="ALU AVENUE">ALU AVENUE</option>
        <option value="ALU SIDI AV">ALU SIDI AV</option>
        <option value="AMARYAWA ST">AMARYAWA ST</option>
        <option value="AMARYAWA ST">AMARYAWA ST</option>
        <option value="AMARYAWA ST">AMARYAWA ST</option>
        <option value="AMINA RD">AMINA RD</option>
        <option value="AMINU DANWAWU ST">AMINU DANWAWU ST</option>
        <option value="AMINU GARBA CL">AMINU GARBA CL</option>
        <option value="AMINU KANO WAY">AMINU KANO WAY</option>
        <option value="AMINU MUDI CL">AMINU MUDI CL</option>
        <option value="AMINU MUDI ST">AMINU MUDI ST</option>
        <option value="ANAMBRA ST">ANAMBRA ST</option>
        <option value="APPLE AV">APPLE AV</option>
        <option value="ARCH. M. T. WAZIRI AV">ARCH. M. T. WAZIRI AV</option>
        <option value="ARCHITECTS LA">ARCHITECTS LA</option>
        <option value="ARKAN RD">ARKAN RD</option>
        <option value="AROCHUKWU LA">AROCHUKWU LA</option>
        <option value="ASALO ST">ASALO ST</option>
        <option value="ASHTON ROAD">ASHTON ROAD</option>
        <option value="ASMU LINK">ASMU LINK</option>
        <option value="ATIKEN RD">ATIKEN RD</option>
        <option value="ATIKU ABUBAKAR ROAD">ATIKU ABUBAKAR ROAD</option>
        <option value="AUDU BAKO RD">AUDU BAKO RD</option>
        <option value="AUDU BAKO WAY">AUDU BAKO WAY</option>
        <option value="AUDU SAMBO  RD">AUDU SAMBO  RD</option>
        <option value="AUDU SAYE ST">AUDU SAYE ST</option>
        <option value="AUDU UTAI ROAD">AUDU UTAI ROAD</option>
        <option value="AUYO ST">AUYO ST</option>
        <option value="AWWALU UMAR KAWAJI LINK">AWWALU UMAR KAWAJI LINK</option>
        <option value="AYAGI ROAD">AYAGI ROAD</option>
        <option value="B.B FAROUK ROAD">B.B FAROUK ROAD</option>
        <option value="BABA JENI RD">BABA JENI RD</option>
        <option value="BABA KUSA STREET">BABA KUSA STREET</option>
        <option value="BABA KUSA STREET">BABA KUSA STREET</option>
        <option value="BABA TSOHO CR">BABA TSOHO CR</option>
        <option value="BABALLE AV">BABALLE AV</option>
        <option value="BABALLE ILA LINK">BABALLE ILA LINK</option>
        <option value="BABAMASI RD">BABAMASI RD</option>
        <option value="BABAN GIJI STREET">BABAN GIJI STREET</option>
        <option value="BABAN KWARI RD">BABAN KWARI RD</option>
        <option value="BABBA GAYA LINE">BABBA GAYA LINE</option>
        <option value="BABBAN LAYI / LAYIN MAI UNGUWA">BABBAN LAYI / LAYIN MAI UNGUWA</option>
        <option value="BABURA LINK">BABURA LINK</option>
        <option value="BABURA RD">BABURA RD</option>
        <option value="BACHIRAWA BABBAN LAYIN ROAD">BACHIRAWA BABBAN LAYIN ROAD</option>
        <option value="BACHIRAWA NRC ROAD">BACHIRAWA NRC ROAD</option>
        <option value="BADAR LINK">BADAR LINK</option>
        <option value="BADAR LINK">BADAR LINK</option>
        <option value="BADAR LINK">BADAR LINK</option>
        <option value="BADAWA RD">BADAWA RD</option>
        <option value="BAFFA RANO LINE">BAFFA RANO LINE</option>
        <option value="BAKAIBINI ST">BAKAIBINI ST</option>
        <option value="BAKASSI RD">BAKASSI RD</option>
        <option value="BAKO MAISHINKU RD">BAKO MAISHINKU RD</option>
        <option value="BALA GAYA ST">BALA GAYA ST</option>
        <option value="BALA HASSAN RD">BALA HASSAN RD</option>
        <option value="BALA MOHAMMED RD">BALA MOHAMMED RD</option>
        <option value="BALA ROAD">BALA ROAD</option>
        <option value="BALARABE ISMAIL ST">BALARABE ISMAIL ST</option>
        <option value="BALBELA ST">BALBELA ST</option>
        <option value="BANK RD">BANK RD</option>
        <option value="BARAITT HUGHES RD">BARAITT HUGHES RD</option>
        <option value="BARANKACI ST">BARANKACI ST</option>
        <option value="BARAU DAMBATTA LINK">BARAU DAMBATTA LINK</option>
        <option value="BARAU DAMBATTA RD">BARAU DAMBATTA RD</option>
        <option value="BARAU DAMBATTA ST">BARAU DAMBATTA ST</option>
        <option value="BARAU INUWA ST">BARAU INUWA ST</option>
        <option value="BARGERY RD">BARGERY RD</option>
        <option value="BARKONO AV">BARKONO AV</option>
        <option value="BARNA ST">BARNA ST</option>
        <option value="BARNA ST">BARNA ST</option>
        <option value="BASHIR ABDULLAHI ST">BASHIR ABDULLAHI ST</option>
        <option value="BASHIR DALHATU AV">BASHIR DALHATU AV</option>
        <option value="BAWO ROAD">BAWO ROAD</option>
        <option value="BAYCO LINE">BAYCO LINE</option>
        <option value="BEBE ST">BEBE ST</option>
        <option value="BEBEJI LINK">BEBEJI LINK</option>
        <option value="BEGUWA ST">BEGUWA ST</option>
        <option value="BEIRUT RD">BEIRUT RD</option>
        <option value="BELA ROAD">BELA ROAD</option>
        <option value="BELLO DANDAGO RD">BELLO DANDAGO RD</option>
        <option value="BELLO KANO TERRACE">BELLO KANO TERRACE</option>
        <option value="BELLO RD">BELLO RD</option>
        <option value="BELLO SANI ALI ST">BELLO SANI ALI ST</option>
        <option value="BILA ST">BILA ST</option>
        <option value="BILA ST">BILA ST</option>
        <option value="BILBILO AV">BILBILO AV</option>
        <option value="BLACK HAMMER ST">BLACK HAMMER ST</option>
        <option value="BOLA ST">BOLA ST</option>
        <option value="BOMPAI ROAD">BOMPAI ROAD</option>
        <option value="BOMPAI ROAD">BOMPAI ROAD</option>
        <option value="BORNO AV">BORNO AV</option>
        <option value="BUHARI QUARTERS CL">BUHARI QUARTERS CL</option>
        <option value="BUHARI QUARTERS RD">BUHARI QUARTERS RD</option>
        <option value="BUHARI QUARTERS ST">BUHARI QUARTERS ST</option>
        <option value="BUK  ROAD">BUK  ROAD</option>
        <option value="BUK  ROAD">BUK  ROAD</option>
        <option value="BUKAVU BARRACK RD">BUKAVU BARRACK RD</option>
        <option value="BUKTAMA ST">BUKTAMA ST</option>
        <option value="BURE ST">BURE ST</option>
        <option value="BUSHIYA AV">BUSHIYA AV</option>
        <option value="C. WARWAI CL">C. WARWAI CL</option>
        <option value="CBN ROAD">CBN ROAD</option>
        <option value="CENTRE LA">CENTRE LA</option>
        <option value="CHURCH RD">CHURCH RD</option>
        <option value="CIJAKI AV">CIJAKI AV</option>
        <option value="CITTA RD">CITTA RD</option>
        <option value="CITY WALL">CITY WALL</option>
        <option value="CIVIC CENTRE ROAD">CIVIC CENTRE ROAD</option>
        <option value="CIVIC CENTRE ROAD">CIVIC CENTRE ROAD</option>
        <option value="CLUB ROAD">CLUB ROAD</option>
        <option value="COMMANDANT CR">COMMANDANT CR</option>
        <option value="COMMISSIONER RD">COMMISSIONER RD</option>
        <option value="COURT ROAD">COURT ROAD</option>
        <option value="COURT ROAD">COURT ROAD</option>
        <option value="DABINAI AV">DABINAI AV</option>
        <option value="DABINO AV">DABINO AV</option>
        <option value="DAE LINE">DAE LINE</option>
        <option value="DAKATA POLICE STATION ROAD">DAKATA POLICE STATION ROAD</option>
        <option value="DAMATSIRI ST">DAMATSIRI ST</option>
        <option value="DAMBATTA ROAD">DAMBATTA ROAD</option>
        <option value="DAMBAZAU RD">DAMBAZAU RD</option>
        <option value="DAMBO ROAD">DAMBO ROAD</option>
        <option value="DAMISA ST">DAMISA ST</option>
        <option value="DAMISA ST">DAMISA ST</option>
        <option value="DAN DABINO ST">DAN DABINO ST</option>
        <option value="DAN DALAMA AV">DAN DALAMA AV</option>
        <option value="DAN DURUMI ROAD">DAN DURUMI ROAD</option>
        <option value="DAN HAUSA STREET">DAN HAUSA STREET</option>
        <option value="DAN WUDIL RD">DAN WUDIL RD</option>
        <option value="DANGORA ST">DANGORA ST</option>
        <option value="DANIYAN KARAYE CL">DANIYAN KARAYE CL</option>
        <option value="DANJUMA ALI GARKO LINE">DANJUMA ALI GARKO LINE</option>
        <option value="DANKULI LINK">DANKULI LINK</option>
        <option value="DANKURA ADAMU ROAD">DANKURA ADAMU ROAD</option>
        <option value="DANKURA LINE">DANKURA LINE</option>
        <option value="DANSHAZAKI LA">DANSHAZAKI LA</option>
        <option value="DANTATA ROAD">DANTATA ROAD</option>
        <option value="DANTATA ROAD">DANTATA ROAD</option>
        <option value="DANTUNKU RD">DANTUNKU RD</option>
        <option value="DANWAWU AV">DANWAWU AV</option>
        <option value="DANWAWU ST">DANWAWU ST</option>
        <option value="DANZOMO ST">DANZOMO ST</option>
        <option value="DARESSALAM LINK">DARESSALAM LINK</option>
        <option value="DATTI AHMED ST">DATTI AHMED ST</option>
        <option value="DA'U ALIYU AV">DA'U ALIYU AV</option>
        <option value="DAUKA RD">DAUKA RD</option>
        <option value="DAWAKI RD">DAWAKI RD</option>
        <option value="DESIGNERS LA">DESIGNERS LA</option>
        <option value="DIKKO LINE">DIKKO LINE</option>
        <option value="DORAWA ROAD">DORAWA ROAD</option>
        <option value="DORAWAR YAN KIFI ROAD">DORAWAR YAN KIFI ROAD</option>
        <option value="DORAYI BABBA ROAD">DORAYI BABBA ROAD</option>
        <option value="DR. ABDULLAHI UMAR GANDUJE ROAD">DR. ABDULLAHI UMAR GANDUJE ROAD</option>
        <option value="DR. ADO BAYERO STREET">DR. ADO BAYERO STREET</option>
        <option value="DR. M SHAMA ST">DR. M SHAMA ST</option>
        <option value="DR. SAMMANI SANI AV">DR. SAMMANI SANI AV</option>
        <option value="DR.G. N. HAMZA ST">DR.G. N. HAMZA ST</option>
        <option value="DUNGURAWA AV">DUNGURAWA AV</option>
        <option value="DUNI STREET">DUNI STREET</option>
        <option value="DURBIN KATSINA">DURBIN KATSINA</option>
        <option value="DURBIN KAZAURE ST">DURBIN KAZAURE ST</option>
        <option value="DURUMI AV">DURUMI AV</option>
        <option value="DURUMI ST">DURUMI ST</option>
        <option value="DUTSE CL">DUTSE CL</option>
        <option value="DUTSE ST">DUTSE ST</option>
        <option value="EASTERN BYE PASS">EASTERN BYE PASS</option>
        <option value="EASTERN BYE PASS">EASTERN BYE PASS</option>
        <option value="EGBE RD">EGBE RD</option>
        <option value="EL-KANEM STREET">EL-KANEM STREET</option>
        <option value="EMIR RD">EMIR RD</option>
        <option value="EMIR'S PALACE ROAD">EMIR'S PALACE ROAD</option>
        <option value="ENG.HABU GUMEL RD">ENG.HABU GUMEL RD</option>
        <option value="ENGINEERS LA">ENGINEERS LA</option>
        <option value="ENUGU RD">ENUGU RD</option>
        <option value="ETSU NUPE ST">ETSU NUPE ST</option>
        <option value="EYE HOSPITAL ROAD">EYE HOSPITAL ROAD</option>
        <option value="FAGWALAWA LINK">FAGWALAWA LINK</option>
        <option value="FAJI RD">FAJI RD</option>
        <option value="FAJI ST">FAJI ST</option>
        <option value="FANISAU ROAD">FANISAU ROAD</option>
        <option value="FARM CENTER ROAD">FARM CENTER ROAD</option>
        <option value="FEDERAL SECRETARIAT ROAD">FEDERAL SECRETARIAT ROAD</option>
        <option value="FOUNDATION RD">FOUNDATION RD</option>
        <option value="FUVE AV">FUVE AV</option>
        <option value="G.M RINGIM ST">G.M RINGIM ST</option>
        <option value="GABASAWA AV">GABASAWA AV</option>
        <option value="GAGARAWA AV">GAGARAWA AV</option>
        <option value="GALADIMA ROAD">GALADIMA ROAD</option>
        <option value="GARBA HASSAN ST">GARBA HASSAN ST</option>
        <option value="GARBA YAKASAI LINK">GARBA YAKASAI LINK</option>
        <option value="GAREJI LINE">GAREJI LINE</option>
        <option value="GARIN GABAS ROAD">GARIN GABAS ROAD</option>
        <option value="GARIYO ST">GARIYO ST</option>
        <option value="GARIYO ST">GARIYO ST</option>
        <option value="GARKI AV">GARKI AV</option>
        <option value="GARKO ST">GARKO ST</option>
        <option value="GARUBA UBALE ST">GARUBA UBALE ST</option>
        <option value="GARUN AV">GARUN AV</option>
        <option value="GARUN MALAM ST">GARUN MALAM ST</option>
        <option value="GASHASH RD">GASHASH RD</option>
        <option value="GAWASA ST">GAWASA ST</option>
        <option value="GAWO AV">GAWO AV</option>
        <option value="GEN DANJUMA ST">GEN DANJUMA ST</option>
        <option value="GENERAL M. BUHARI STREET">GENERAL M. BUHARI STREET</option>
        <option value="GETSO ST">GETSO ST</option>
        <option value="GIDADO IDRIS AV">GIDADO IDRIS AV</option>
        <option value="GIDADO RD">GIDADO RD</option>
        <option value="GIDAN FIAT LINE">GIDAN FIAT LINE</option>
        <option value="GIDAN MAKARA ST">GIDAN MAKARA ST</option>
        <option value="GIDAN MALALA ST">GIDAN MALALA ST</option>
        <option value="GIMBA UMAR CL">GIMBA UMAR CL</option>
        <option value="GIWA ST">GIWA ST</option>
        <option value="GOBA LINK">GOBA LINK</option>
        <option value="GOBIN KWANO LINE">GOBIN KWANO LINE</option>
        <option value="GODIYA AV">GODIYA AV</option>
        <option value="GUDA ABDULLAHI ROAD">GUDA ABDULLAHI ROAD</option>
        <option value="GUGA LINE">GUGA LINE</option>
        <option value="GUMEL LINE">GUMEL LINE</option>
        <option value="GUMEL ROAD">GUMEL ROAD</option>
        <option value="GURNA RD">GURNA RD</option>
        <option value="GURUZA ST">GURUZA ST</option>
        <option value="GWANDA AV">GWANDA AV</option>
        <option value="GWARAM STREET">GWARAM STREET</option>
        <option value="GWARZO ROAD">GWARZO ROAD</option>
        <option value="GWARZO/KATSINA LINK ROAD">GWARZO/KATSINA LINK ROAD</option>
        <option value="GWARZO/KATSINA LINK ROAD">GWARZO/KATSINA LINK ROAD</option>
        <option value="GYAUKAWA AV">GYAUKAWA AV</option>
        <option value="H. USMAN KATSINA AV">H. USMAN KATSINA AV</option>
        <option value="HABA LINK">HABA LINK</option>
        <option value="HABIB GWARZO RD">HABIB GWARZO RD</option>
        <option value="HADEIJA ROAD">HADEIJA ROAD</option>
        <option value="HADEJIA ROAD">HADEJIA ROAD</option>
        <option value="HAJIYA ALTINE ST">HAJIYA ALTINE ST</option>
        <option value="HAJJ CAMP ROAD">HAJJ CAMP ROAD</option>
        <option value="HALIRU GWARZO RD">HALIRU GWARZO RD</option>
        <option value="HAMCO HOTEL ST">HAMCO HOTEL ST</option>
        <option value="HAMZA ABDULLAHI RD">HAMZA ABDULLAHI RD</option>
        <option value="HANGA AV">HANGA AV</option>
        <option value="HARUNA UNGOGO RD">HARUNA UNGOGO RD</option>
        <option value="HASSAN DANMUAZU ROAD">HASSAN DANMUAZU ROAD</option>
        <option value="HAUSAWA YAN FULANI ROAD">HAUSAWA YAN FULANI ROAD</option>
        <option value="HAUWA MASAKA ST">HAUWA MASAKA ST</option>
        <option value="HEAD OF SERVICE LINE">HEAD OF SERVICE LINE</option>
        <option value="HECTOR ST">HECTOR ST</option>
        <option value="HIGH TENSION LINE">HIGH TENSION LINE</option>
        <option value="HOSPITAL ROAD">HOSPITAL ROAD</option>
        <option value="HOSPITAL ROAD">HOSPITAL ROAD</option>
        <option value="I.T.F. ST">I.T.F. ST</option>
        <option value="IBADAN ST">IBADAN ST</option>
        <option value="IBB WAY">IBB WAY</option>
        <option value="IBB WAY">IBB WAY</option>
        <option value="IBO LINE">IBO LINE</option>
        <option value="IBRAHIM DABO RD">IBRAHIM DABO RD</option>
        <option value="IBRAHIM KEMBA CL">IBRAHIM KEMBA CL</option>
        <option value="IBRAHIM MAI HIACE ST">IBRAHIM MAI HIACE ST</option>
        <option value="IBRAHIM TAIWO ROAD">IBRAHIM TAIWO ROAD</option>
        <option value="IBRAHIM TAIWO ROAD">IBRAHIM TAIWO ROAD</option>
        <option value="IBRAHIM TAIWO ROAD">IBRAHIM TAIWO ROAD</option>
        <option value="IBRAHIM TAIWO ROAD">IBRAHIM TAIWO ROAD</option>
        <option value="IBRAHIM TASO ST">IBRAHIM TASO ST</option>
        <option value="IBRAHIM UMAR ST">IBRAHIM UMAR ST</option>
        <option value="IBRAHIM ZUBAIRU ST">IBRAHIM ZUBAIRU ST</option>
        <option value="IGBO ROAD">IGBO ROAD</option>
        <option value="IJEBU RD">IJEBU RD</option>
        <option value="ILARO RD">ILARO RD</option>
        <option value="ILLO RD">ILLO RD</option>
        <option value="ILORIN RD">ILORIN RD</option>
        <option value="INDEPENDENCE ROAD">INDEPENDENCE ROAD</option>
        <option value="INEC LINE">INEC LINE</option>
        <option value="INIBI ST">INIBI ST</option>
        <option value="IROKO AV">IROKO AV</option>
        <option value="ISA AHMED MAITURARE ST">ISA AHMED MAITURARE ST</option>
        <option value="ISA KACHAKO RD">ISA KACHAKO RD</option>
        <option value="ISA WALI STREET">ISA WALI STREET</option>
        <option value="ISA WAZIRI ROAD">ISA WAZIRI ROAD</option>
        <option value="ISAH RINGIM ST">ISAH RINGIM ST</option>
        <option value="ISIAMIYYA AV">ISIAMIYYA AV</option>
        <option value="IYAKA LINK">IYAKA LINK</option>
        <option value="JAJIRA ROAD">JAJIRA ROAD</option>
        <option value="JAKARA DR">JAKARA DR</option>
        <option value="JAKARA DR">JAKARA DR</option>
        <option value="JANKASA CL">JANKASA CL</option>
        <option value="JANRUWA AV">JANRUWA AV</option>
        <option value="JAOJI BABBA">JAOJI BABBA</option>
        <option value="JAOJI KARAMA RD">JAOJI KARAMA RD</option>
        <option value="JAOJI RD">JAOJI RD</option>
        <option value="JEGA RD">JEGA RD</option>
        <option value="JIGAWA RD">JIGAWA RD</option>
        <option value="JIGAWA RD">JIGAWA RD</option>
        <option value="JUNJUMI ST">JUNJUMI ST</option>
        <option value="JUSTREETICE D. MUSTAPHA ROAD">JUSTREETICE D. MUSTAPHA ROAD</option>
        <option value="JUWACO LINE">JUWACO LINE</option>
        <option value="KABBA ST">KABBA ST</option>
        <option value="KABIRU KOKI AV">KABIRU KOKI AV</option>
        <option value="KABO AV">KABO AV</option>
        <option value="KABOBO ST">KABOBO ST</option>
        <option value="KADAWA AV">KADAWA AV</option>
        <option value="KAFIN HAUSA ST">KAFIN HAUSA ST</option>
        <option value="KAGUWA ST">KAGUWA ST</option>
        <option value="KAIBARI CL">KAIBARI CL</option>
        <option value="KALU ST">KALU ST</option>
        <option value="KANGAW RD">KANGAW RD</option>
        <option value="KANGO LINE">KANGO LINE</option>
        <option value="KANTAUSA AV">KANTAUSA AV</option>
        <option value="KANYA AV">KANYA AV</option>
        <option value="KANYA AV">KANYA AV</option>
        <option value="KAPITAL INSURANCE ST">KAPITAL INSURANCE ST</option>
        <option value="KARA ST">KARA ST</option>
        <option value="KARAI AV">KARAI AV</option>
        <option value="KARAI CL">KARAI CL</option>
        <option value="KASHIM IBRAHIM ROAD">KASHIM IBRAHIM ROAD</option>
        <option value="KASKA ST">KASKA ST</option>
        <option value="KASKO LA">KASKO LA</option>
        <option value="KASSIM ST">KASSIM ST</option>
        <option value="KASUWAN DANMAI LALLE LINE">KASUWAN DANMAI LALLE LINE</option>
        <option value="KASUWAR KURMI ROAD">KASUWAR KURMI ROAD</option>
        <option value="KATSINA ROAD">KATSINA ROAD</option>
        <option value="KATSINA ROAD">KATSINA ROAD</option>
        <option value="KATSINA ROAD">KATSINA ROAD</option>
        <option value="KATSINA ROAD">KATSINA ROAD</option>
        <option value="KATSINAWA/AMASAWA/CHIROMAWA ROAD">KATSINAWA/AMASAWA/CHIROMAWA ROAD</option>
        <option value="KAWAJI ROAD">KAWAJI ROAD</option>
        <option value="KAWON MAIGARI RD">KAWON MAIGARI RD</option>
        <option value="KAWU LINE">KAWU LINE</option>
        <option value="KAZAURE RD">KAZAURE RD</option>
        <option value="KIBIYA LINE">KIBIYA LINE</option>
        <option value="KIBIYA ST">KIBIYA ST</option>
        <option value="KIBIYA ST">KIBIYA ST</option>
        <option value="KINGS GAROADEN RD">KINGS GAROADEN RD</option>
        <option value="KIRFI ST">KIRFI ST</option>
        <option value="KIRU ST">KIRU ST</option>
        <option value="KIYASHI AV">KIYASHI AV</option>
        <option value="KIYYAYA AV">KIYYAYA AV</option>
        <option value="KOFAR DAWANAU ROAD">KOFAR DAWANAU ROAD</option>
        <option value="KOFAR FAMFO ROAD">KOFAR FAMFO ROAD</option>
        <option value="KOFAR MAZUGAL ROAD">KOFAR MAZUGAL ROAD</option>
        <option value="KOFAR RUWA ROAD">KOFAR RUWA ROAD</option>
        <option value="KOKI ST">KOKI ST</option>
        <option value="KORAU ROAD">KORAU ROAD</option>
        <option value="KUMURYA AV">KUMURYA AV</option>
        <option value="KUNDILA ROAD">KUNDILA ROAD</option>
        <option value="KURA MUHAMMED ST">KURA MUHAMMED ST</option>
        <option value="KUREGE AV">KUREGE AV</option>
        <option value="KURUMI MARKET ROAD">KURUMI MARKET ROAD</option>
        <option value="KWA CL">KWA CL</option>
        <option value="KWAI AV">KWAI AV</option>
        <option value="KWAIRANGA RD">KWAIRANGA RD</option>
        <option value="KWA-KWA AV">KWA-KWA AV</option>
        <option value="KWANAR GAYE  ST">KWANAR GAYE  ST</option>
        <option value="KWANAR K.C RD">KWANAR K.C RD</option>
        <option value="KWANAR MASALLACHI RD">KWANAR MASALLACHI RD</option>
        <option value="KWANAR UNGOGO ROAD">KWANAR UNGOGO ROAD</option>
        <option value="KYAWA ST">KYAWA ST</option>
        <option value="LAFIA RD">LAFIA RD</option>
        <option value="LAJAWA ST">LAJAWA ST</option>
        <option value="LAJAWA ST">LAJAWA ST</option>
        <option value="LAMIDO CR">LAMIDO CR</option>
        <option value="LAMIDO TERRACE">LAMIDO TERRACE</option>
        <option value="LARABA ROAD">LARABA ROAD</option>
        <option value="LAUTAI RD">LAUTAI RD</option>
        <option value="LAWAN DAMBAZAU ST">LAWAN DAMBAZAU ST</option>
        <option value="LAYIN ADAMU SORONDINKI">LAYIN ADAMU SORONDINKI</option>
        <option value="LAYIN ALH. ADO">LAYIN ALH. ADO</option>
        <option value="LAYIN ALH. AWWALU SUPA">LAYIN ALH. AWWALU SUPA</option>
        <option value="LAYIN ALH. HAMZA">LAYIN ALH. HAMZA</option>
        <option value="LAYIN ALH. KAWU">LAYIN ALH. KAWU</option>
        <option value="LAYIN ALHUDA">LAYIN ALHUDA</option>
        <option value="LAYIN BAURE">LAYIN BAURE</option>
        <option value="LAYIN BAYAN SECRETARIAT">LAYIN BAYAN SECRETARIAT</option>
        <option value="LAYIN BETHMA">LAYIN BETHMA</option>
        <option value="LAYIN CHAIRMAN">LAYIN CHAIRMAN</option>
        <option value="LAYIN CHEDIYA">LAYIN CHEDIYA</option>
        <option value="LAYIN DANBAKALE">LAYIN DANBAKALE</option>
        <option value="LAYIN DANDISHE">LAYIN DANDISHE</option>
        <option value="LAYIN DANZANGO">LAYIN DANZANGO</option>
        <option value="LAYIN DAUDA">LAYIN DAUDA</option>
        <option value="LAYIN DINYA">LAYIN DINYA</option>
        <option value="LAYIN DR. JIBO">LAYIN DR. JIBO</option>
        <option value="LAYIN FARIN BENE">LAYIN FARIN BENE</option>
        <option value="LAYIN GAREJ">LAYIN GAREJ</option>
        <option value="LAYIN GAREJI">LAYIN GAREJI</option>
        <option value="LAYIN GIDAN ALH. SULE">LAYIN GIDAN ALH. SULE</option>
        <option value="LAYIN GIDAN ALKALI">LAYIN GIDAN ALKALI</option>
        <option value="LAYIN GIDAN DAKIN">LAYIN GIDAN DAKIN</option>
        <option value="LAYIN GIDAN DARMA">LAYIN GIDAN DARMA</option>
        <option value="LAYIN GIDAN KARA">LAYIN GIDAN KARA</option>
        <option value="LAYIN GIDAN KIFI">LAYIN GIDAN KIFI</option>
        <option value="LAYIN GIDAN RIRIWAI / NURUDEEN ISLAMIYYA">LAYIN GIDAN RIRIWAI / NURUDEEN ISLAMIYYA</option>
        <option value="LAYIN GIDAN RUWA">LAYIN GIDAN RUWA</option>
        <option value="LAYIN GIGINYA">LAYIN GIGINYA</option>
        <option value="LAYIN HAMZA RINGIM">LAYIN HAMZA RINGIM</option>
        <option value="LAYIN INEPA">LAYIN INEPA</option>
        <option value="LAYIN ISLAMMIYA">LAYIN ISLAMMIYA</option>
        <option value="LAYIN IZALA">LAYIN IZALA</option>
        <option value="LAYIN JANBULO">LAYIN JANBULO</option>
        <option value="LAYIN KARAGE">LAYIN KARAGE</option>
        <option value="LAYIN KUKA">LAYIN KUKA</option>
        <option value="LAYIN KURA">LAYIN KURA</option>
        <option value="LAYIN LAWAN RANO">LAYIN LAWAN RANO</option>
        <option value="LAYIN M. T. BADAMASI">LAYIN M. T. BADAMASI</option>
        <option value="LAYIN MAI AKOKO">LAYIN MAI AKOKO</option>
        <option value="LAYIN MAIGARI">LAYIN MAIGARI</option>
        <option value="LAYIN MAIUNGUWA ILIYA">LAYIN MAIUNGUWA ILIYA</option>
        <option value="LAYIN MAKABARTA">LAYIN MAKABARTA</option>
        <option value="LAYIN MALAM BELLO">LAYIN MALAM BELLO</option>
        <option value="LAYIN MAYANKA">LAYIN MAYANKA</option>
        <option value="LAYIN MUSA AMINU">LAYIN MUSA AMINU</option>
        <option value="LAYIN MUSA ILLIYASU">LAYIN MUSA ILLIYASU</option>
        <option value="LAYIN POLICE STREETATION">LAYIN POLICE STREETATION</option>
        <option value="LAYIN RADIANCE">LAYIN RADIANCE</option>
        <option value="LAYIN RD SAFETY">LAYIN RD SAFETY</option>
        <option value="LAYIN RIJIYA">LAYIN RIJIYA</option>
        <option value="LAYIN SAKATARE">LAYIN SAKATARE</option>
        <option value="LAYIN SANI KUBEWA">LAYIN SANI KUBEWA</option>
        <option value="LAYIN SECRETARIAT">LAYIN SECRETARIAT</option>
        <option value="LAYIN SHEIK IBRAHIM KHAUFA">LAYIN SHEIK IBRAHIM KHAUFA</option>
        <option value="LAYIN SHEKARE">LAYIN SHEKARE</option>
        <option value="LAYIN SHUGABA TANKO">LAYIN SHUGABA TANKO</option>
        <option value="LAYIN SITTING ROOM">LAYIN SITTING ROOM</option>
        <option value="LAYIN TABARU">LAYIN TABARU</option>
        <option value="LAYIN TAOBAT">LAYIN TAOBAT</option>
        <option value="LAYIN TRANSFORMER">LAYIN TRANSFORMER</option>
        <option value="LAYIN TURAWA">LAYIN TURAWA</option>
        <option value="LAYIN UBALE CUSTOM">LAYIN UBALE CUSTOM</option>
        <option value="LAYIN WHITE HOUSE">LAYIN WHITE HOUSE</option>
        <option value="LAYIN YAHAYA MAIKARE">LAYIN YAHAYA MAIKARE</option>
        <option value="LAYIN YAN KWADI">LAYIN YAN KWADI</option>
        <option value="LAYIN YAN LEMO">LAYIN YAN LEMO</option>
        <option value="LAYIN YUSUF SHUAIBU">LAYIN YUSUF SHUAIBU</option>
        <option value="LIMAN BASIRU ST">LIMAN BASIRU ST</option>
        <option value="LIMAN JALLE ROAD">LIMAN JALLE ROAD</option>
        <option value="LINK 1">LINK 1</option>
        <option value="LINK 10">LINK 10</option>
        <option value="LINK 11">LINK 11</option>
        <option value="LINK 12">LINK 12</option>
        <option value="LINK 13">LINK 13</option>
        <option value="LINK 14">LINK 14</option>
        <option value="LINK 15">LINK 15</option>
        <option value="LINK 16">LINK 16</option>
        <option value="LINK 17">LINK 17</option>
        <option value="LINK 18">LINK 18</option>
        <option value="LINK 19">LINK 19</option>
        <option value="LINK 2">LINK 2</option>
        <option value="LINK 2">LINK 2</option>
        <option value="LINK 20">LINK 20</option>
        <option value="LINK 3">LINK 3</option>
        <option value="LINK 3">LINK 3</option>
        <option value="LINK 4">LINK 4</option>
        <option value="LINK 4">LINK 4</option>
        <option value="LINK 5">LINK 5</option>
        <option value="LINK 5">LINK 5</option>
        <option value="LINK 6">LINK 6</option>
        <option value="LINK 6">LINK 6</option>
        <option value="LINK 7">LINK 7</option>
        <option value="LINK 7">LINK 7</option>
        <option value="LINK 8">LINK 8</option>
        <option value="LINK 9">LINK 9</option>
        <option value="LODGE ROAD">LODGE ROAD</option>
        <option value="LUGGARD AV">LUGGARD AV</option>
        <option value="LUNKUI STREET">LUNKUI STREET</option>
        <option value="MADOBI ROAD">MADOBI ROAD</option>
        <option value="MADUGU LINK">MADUGU LINK</option>
        <option value="MADUGU NABAKON WAYA ST">MADUGU NABAKON WAYA ST</option>
        <option value="MAGAJIN RUMFA ROAD">MAGAJIN RUMFA ROAD</option>
        <option value="MAGANDA ROAD">MAGANDA ROAD</option>
        <option value="MAGANDA ROAD">MAGANDA ROAD</option>
        <option value="MAGANDA ROAD">MAGANDA ROAD</option>
        <option value="MAI DILE RD">MAI DILE RD</option>
        <option value="MAIDUGURI ROAD">MAIDUGURI ROAD</option>
        <option value="MAIDUGURI ROAD">MAIDUGURI ROAD</option>
        <option value="MAIGAOON KAYA ST">MAIGAOON KAYA ST</option>
        <option value="MAIGARI ST">MAIGARI ST</option>
        <option value="MAIMALARI  ROAD">MAIMALARI  ROAD</option>
        <option value="MAIMUNA LINK">MAIMUNA LINK</option>
        <option value="MAITAMA SULE AV">MAITAMA SULE AV</option>
        <option value="MAKERA AV">MAKERA AV</option>
        <option value="MAKERA AVENUE">MAKERA AVENUE</option>
        <option value="MAKERA AVENUE">MAKERA AVENUE</option>
        <option value="MAKWARARI STREET">MAKWARARI STREET</option>
        <option value="MAL. HABIB HASSAN AVENUE">MAL. HABIB HASSAN AVENUE</option>
        <option value="MALAM BABBA ST">MALAM BABBA ST</option>
        <option value="MALAM NA MADABO ROAD">MALAM NA MADABO ROAD</option>
        <option value="MALAM SALGA STREET">MALAM SALGA STREET</option>
        <option value="MALAM SANI ADAWO LAFIYA ST">MALAM SANI ADAWO LAFIYA ST</option>
        <option value="MALLAM ARI">MALLAM ARI</option>
        <option value="MALLAM BAKATSINE RD">MALLAM BAKATSINE RD</option>
        <option value="MALLAM IKKI RD">MALLAM IKKI RD</option>
        <option value="MALLAM KWARU ST">MALLAM KWARU ST</option>
        <option value="MALLAM MADORI ST">MALLAM MADORI ST</option>
        <option value="MALLAM SULE ST">MALLAM SULE ST</option>
        <option value="MANDAWARI SABONTITI ROAD">MANDAWARI SABONTITI ROAD</option>
        <option value="MANDAWARI SABONTITI ROAD">MANDAWARI SABONTITI ROAD</option>
        <option value="MARHABA CLINIC RD">MARHABA CLINIC RD</option>
        <option value="MARKET ROAD">MARKET ROAD</option>
        <option value="MASALLACHI CR">MASALLACHI CR</option>
        <option value="MASALLACHI LINE">MASALLACHI LINE</option>
        <option value="MASALLACHI ST">MASALLACHI ST</option>
        <option value="MASALLACHI ST">MASALLACHI ST</option>
        <option value="MASALLACHI TABARISA LINE">MASALLACHI TABARISA LINE</option>
        <option value="MASAMA CL">MASAMA CL</option>
        <option value="MATAN FADA ROAD">MATAN FADA ROAD</option>
        <option value="MATASA ST">MATASA ST</option>
        <option value="MAYU RD">MAYU RD</option>
        <option value="MEDICAL AV">MEDICAL AV</option>
        <option value="MIDDLE RD">MIDDLE RD</option>
        <option value="MIKIYA ST">MIKIYA ST</option>
        <option value="MILLER ROAD">MILLER ROAD</option>
        <option value="MISSION ROAD">MISSION ROAD</option>
        <option value="MIYANGU CL">MIYANGU CL</option>
        <option value="MIYANGU RD">MIYANGU RD</option>
        <option value="MODIBBO ADAMU ST">MODIBBO ADAMU ST</option>
        <option value="MOHAMMADU MAUDE AV">MOHAMMADU MAUDE AV</option>
        <option value="MOHAMMADU RUMFA ROAD">MOHAMMADU RUMFA ROAD</option>
        <option value="MOHAMMED GAUYAMA AV">MOHAMMED GAUYAMA AV</option>
        <option value="MOHAMMED KYABO LINK">MOHAMMED KYABO LINK</option>
        <option value="MOHAMMED SAGIR WAZIRI ST">MOHAMMED SAGIR WAZIRI ST</option>
        <option value="MOHAMMED YAHAYA KYALO ST">MOHAMMED YAHAYA KYALO ST</option>
        <option value="MOHD GWARZO AV">MOHD GWARZO AV</option>
        <option value="MOHD MOHD AV">MOHD MOHD AV</option>
        <option value="MOHD VICE ADAMU CL">MOHD VICE ADAMU CL</option>
        <option value="MOHD VICE ADAMU ST">MOHD VICE ADAMU ST</option>
        <option value="MOHD. CUSTOM ST">MOHD. CUSTOM ST</option>
        <option value="MOHD. GWARZO ST">MOHD. GWARZO ST</option>
        <option value="MOHD. NASIR MUKTHAR CL">MOHD. NASIR MUKTHAR CL</option>
        <option value="MUAZU HAMZA ST">MUAZU HAMZA ST</option>
        <option value="MUDI ALASAN RD">MUDI ALASAN RD</option>
        <option value="MUKTAR MAIDABINO ST">MUKTAR MAIDABINO ST</option>
        <option value="MUKTARI HASSAN CL">MUKTARI HASSAN CL</option>
        <option value="MUNDUBAWA AVENUE">MUNDUBAWA AVENUE</option>
        <option value="MUNTAKA ST">MUNTAKA ST</option>
        <option value="MURTALA MOHAMMED WAY">MURTALA MOHAMMED WAY</option>
        <option value="MURTALA MOHAMMED WAY">MURTALA MOHAMMED WAY</option>
        <option value="MURTALA MOHAMMED WAY">MURTALA MOHAMMED WAY</option>
        <option value="MURTALA MOHAMMED WAY">MURTALA MOHAMMED WAY</option>
        <option value="MURTALA MOHAMMED WAY">MURTALA MOHAMMED WAY</option>
        <option value="MURZU ST">MURZU ST</option>
        <option value="MUSA BORODO RD">MUSA BORODO RD</option>
        <option value="MUSA KOFA CL">MUSA KOFA CL</option>
        <option value="MUSA U.A.C ST">MUSA U.A.C ST</option>
        <option value="NABABA BADAMASI ROAD">NABABA BADAMASI ROAD</option>
        <option value="NAGERO ST">NAGERO ST</option>
        <option value="NAGWABA ST">NAGWABA ST</option>
        <option value="NAIBAWA CENTRAL PARK RD">NAIBAWA CENTRAL PARK RD</option>
        <option value="NASIRU KABARA AV">NASIRU KABARA AV</option>
        <option value="NASIRU SAMINU LINK">NASIRU SAMINU LINK</option>
        <option value="NEPA/ ODILI ST">NEPA/ ODILI ST</option>
        <option value="NEW AIRPORT ROAD">NEW AIRPORT ROAD</option>
        <option value="NEW HOSPITAL ROAD">NEW HOSPITAL ROAD</option>
        <option value="NEW RACE COURSE ROAD">NEW RACE COURSE ROAD</option>
        <option value="NEW ROAD">NEW ROAD</option>
        <option value="NGURU AVENUE">NGURU AVENUE</option>
        <option value="NIGER RD">NIGER RD</option>
        <option value="NIGER STREET">NIGER STREET</option>
        <option value="NNPC AV">NNPC AV</option>
        <option value="NSUKKA ST">NSUKKA ST</option>
        <option value="OBASANJO ROAD">OBASANJO ROAD</option>
        <option value="ODUTOLA STREET">ODUTOLA STREET</option>
        <option value="OKENE STREET">OKENE STREET</option>
        <option value="OLD CEMENTARY ROAD">OLD CEMENTARY ROAD</option>
        <option value="ONITSHA RD">ONITSHA RD</option>
        <option value="OYO ST">OYO ST</option>
        <option value="PALMER RD">PALMER RD</option>
        <option value="PANDAUDU STREET">PANDAUDU STREET</option>
        <option value="PANSHEKARA ROAD">PANSHEKARA ROAD</option>
        <option value="PANSHEKARA ROAD">PANSHEKARA ROAD</option>
        <option value="PANSHEKARA ROAD">PANSHEKARA ROAD</option>
        <option value="PARK RD">PARK RD</option>
        <option value="PLANNERS LA">PLANNERS LA</option>
        <option value="POLICE BARRACK ROAD">POLICE BARRACK ROAD</option>
        <option value="POLICE BARRACK ROAD">POLICE BARRACK ROAD</option>
        <option value="POLICEMAN'S WALK">POLICEMAN'S WALK</option>
        <option value="POST OFFICE ROAD">POST OFFICE ROAD</option>
        <option value="PRESIDENT AVENUE">PRESIDENT AVENUE</option>
        <option value="PROF. IBRAHIM GARBA LINE">PROF. IBRAHIM GARBA LINE</option>
        <option value="PROPOSED WESTERN BYE PASS">PROPOSED WESTERN BYE PASS</option>
        <option value="PROPOSED WESTERN BYE PASS">PROPOSED WESTERN BYE PASS</option>
        <option value="PROPOSED WESTERN BYE-PASS">PROPOSED WESTERN BYE-PASS</option>
        <option value="RACE COURSE ROAD">RACE COURSE ROAD</option>
        <option value="RACE COURSE ROAD">RACE COURSE ROAD</option>
        <option value="RAFIN KUKA">RAFIN KUKA</option>
        <option value="RAHAMA ST">RAHAMA ST</option>
        <option value="RAKUMI AV">RAKUMI AV</option>
        <option value="RANDA LA">RANDA LA</option>
        <option value="RD 1">RD 1</option>
        <option value="RD 11">RD 11</option>
        <option value="RIBADU ROAD">RIBADU ROAD</option>
        <option value="RIMA ST">RIMA ST</option>
        <option value="RIMI CL">RIMI CL</option>
        <option value="ROGO AV">ROGO AV</option>
        <option value="ROYAL ST">ROYAL ST</option>
        <option value="RUNHU AV">RUNHU AV</option>
        <option value="S. K. D. LINE">S. K. D. LINE</option>
        <option value="S/MANDAWARI ST">S/MANDAWARI ST</option>
        <option value="SA'AD TANKO RD">SA'AD TANKO RD</option>
        <option value="SABO BAKIN ZUWO ROAD">SABO BAKIN ZUWO ROAD</option>
        <option value="SABO DANTATA CR">SABO DANTATA CR</option>
        <option value="SABON BIRNIN KWACHIRI ROAD">SABON BIRNIN KWACHIRI ROAD</option>
        <option value="SABON MASALLACHI ST">SABON MASALLACHI ST</option>
        <option value="SABON TITI DAN RIMI ROAD">SABON TITI DAN RIMI ROAD</option>
        <option value="SABUWAR GADUN RD">SABUWAR GADUN RD</option>
        <option value="SAGIR KUMASI RD">SAGIR KUMASI RD</option>
        <option value="SALIHI ILLIYASU AV">SALIHI ILLIYASU AV</option>
        <option value="SALIHU GALEEL LINK">SALIHU GALEEL LINK</option>
        <option value="SALLARI JUNCTION RD">SALLARI JUNCTION RD</option>
        <option value="SAMADI ST">SAMADI ST</option>
        <option value="SANI ABACHA WAY">SANI ABACHA WAY</option>
        <option value="SANI ABACHA WAY">SANI ABACHA WAY</option>
        <option value="SANI BELLO ROAD">SANI BELLO ROAD</option>
        <option value="SANI BROTHERS LINK">SANI BROTHERS LINK</option>
        <option value="SANI GARBA ST">SANI GARBA ST</option>
        <option value="SANI GIWA ST">SANI GIWA ST</option>
        <option value="SANI KABARA ROAD">SANI KABARA ROAD</option>
        <option value="SANI RANO CL">SANI RANO CL</option>
        <option value="SANI STARLET ST">SANI STARLET ST</option>
        <option value="SANI YARO ST">SANI YARO ST</option>
        <option value="SANTOS LINE">SANTOS LINE</option>
        <option value="SARKI LABARAN LINE">SARKI LABARAN LINE</option>
        <option value="SARKI RD">SARKI RD</option>
        <option value="SARKIN FAWA LINE">SARKIN FAWA LINE</option>
        <option value="SARKIN HATSI ST">SARKIN HATSI ST</option>
        <option value="SARKIN YAKI ROAD">SARKIN YAKI ROAD</option>
        <option value="SAROADAUNA CR">SAROADAUNA CR</option>
        <option value="SAUDE MAIDIRISHI ST">SAUDE MAIDIRISHI ST</option>
        <option value="SAYAOHI ST">SAYAOHI ST</option>
        <option value="SHAHO ST">SHAHO ST</option>
        <option value="SHEHU ABUBAKAR HASSAN ST">SHEHU ABUBAKAR HASSAN ST</option>
        <option value="SHEHU ALIYU HADEJIA ST">SHEHU ALIYU HADEJIA ST</option>
        <option value="SHEHU AZARE ST">SHEHU AZARE ST</option>
        <option value="SHEHU DAWAKI AV">SHEHU DAWAKI AV</option>
        <option value="SHEHU GARBA ALI LINK">SHEHU GARBA ALI LINK</option>
        <option value="SHEHU KABIRU BAYERO RD">SHEHU KABIRU BAYERO RD</option>
        <option value="SHEHU KAZAURE ROAD">SHEHU KAZAURE ROAD</option>
        <option value="SHEHU MAIHATSI STREET">SHEHU MAIHATSI STREET</option>
        <option value="SHEHU MANZO ARZAI STREET">SHEHU MANZO ARZAI STREET</option>
        <option value="SHEHU MINJIBIR AV">SHEHU MINJIBIR AV</option>
        <option value="SHEHU NA ALLAH ST">SHEHU NA ALLAH ST</option>
        <option value="SHEHU NA ALLAH ST">SHEHU NA ALLAH ST</option>
        <option value="SHEHU TIJJANI ST">SHEHU TIJJANI ST</option>
        <option value="SHEHU UBA ROAD">SHEHU UBA ROAD</option>
        <option value="SHEHU UBA ROAD">SHEHU UBA ROAD</option>
        <option value="SHEHU ZARANDA ST">SHEHU ZARANDA ST</option>
        <option value="SHEIK AHMAD DEEDAT ROAD">SHEIK AHMAD DEEDAT ROAD</option>
        <option value="SHEIK IBRAHIM ABUBAKAR RAMADAN ROAD">SHEIK IBRAHIM ABUBAKAR RAMADAN ROAD</option>
        <option value="SHEIK TIJJANI USMAN ROAD">SHEIK TIJJANI USMAN ROAD</option>
        <option value="SHEKARA ROAD">SHEKARA ROAD</option>
        <option value="SHEKARE ST">SHEKARE ST</option>
        <option value="SHUAIBU KAZAURE RD">SHUAIBU KAZAURE RD</option>
        <option value="SHUWAKA AV">SHUWAKA AV</option>
        <option value="SOJA LINE">SOJA LINE</option>
        <option value="SOKOTO ROAD">SOKOTO ROAD</option>
        <option value="ST. LOUIS AVENUE">ST. LOUIS AVENUE</option>
        <option value="STADIUM ROAD">STADIUM ROAD</option>
        <option value="STATE ROAD">STATE ROAD</option>
        <option value="STATE ROAD">STATE ROAD</option>
        <option value="SULE BAKI AV">SULE BAKI AV</option>
        <option value="SULE BATSARI AV">SULE BATSARI AV</option>
        <option value="SULE GAYA ROAD">SULE GAYA ROAD</option>
        <option value="SULE MOHD DAMDATTA AV">SULE MOHD DAMDATTA AV</option>
        <option value="SULEIMAN GEZAWA ROAD">SULEIMAN GEZAWA ROAD</option>
        <option value="SULEIMAN ROAD">SULEIMAN ROAD</option>
        <option value="SULTAN RD">SULTAN RD</option>
        <option value="SUMAILA MUKHTAR MAI BISCUIT ROAD">SUMAILA MUKHTAR MAI BISCUIT ROAD</option>
        <option value="SURAJO MARSHALL LINK">SURAJO MARSHALL LINK</option>
        <option value="SURVEYORS LA">SURVEYORS LA</option>
        <option value="TAFAWA BALEWA ROAD">TAFAWA BALEWA ROAD</option>
        <option value="TAFAWA BALEWA ROAD">TAFAWA BALEWA ROAD</option>
        <option value="TAGWAYEN GIDA STREET">TAGWAYEN GIDA STREET</option>
        <option value="TAKAI AV">TAKAI AV</option>
        <option value="TAMANDU CL">TAMANDU CL</option>
        <option value="TAMANDU RD">TAMANDU RD</option>
        <option value="TANKO YAKASAI ST">TANKO YAKASAI ST</option>
        <option value="TAPASA ST">TAPASA ST</option>
        <option value="TARAUNI MARKET ROAD">TARAUNI MARKET ROAD</option>
        <option value="TATA GANA STREET">TATA GANA STREET</option>
        <option value="TONKU AV">TONKU AV</option>
        <option value="TRADE FAIR COMPLEX RD">TRADE FAIR COMPLEX RD</option>
        <option value="TSAGE AV">TSAGE AV</option>
        <option value="TUDUN BOJUWA BABBAN LINE">TUDUN BOJUWA BABBAN LINE</option>
        <option value="TUDUN MALIKI ROAD">TUDUN MALIKI ROAD</option>
        <option value="TUDUN MURTALA ROAD">TUDUN MURTALA ROAD</option>
        <option value="TUKUNTAWA ROAD">TUKUNTAWA ROAD</option>
        <option value="TUKUR ROAD">TUKUR ROAD</option>
        <option value="TULU LA">TULU LA</option>
        <option value="UBA IDRIS CL">UBA IDRIS CL</option>
        <option value="UBA LIDA ST">UBA LIDA ST</option>
        <option value="UDB ROAD">UDB ROAD</option>
        <option value="UK AV">UK AV</option>
        <option value="UMAR ADAMU KAWAJI ST">UMAR ADAMU KAWAJI ST</option>
        <option value="UMAR BABURA LINK">UMAR BABURA LINK</option>
        <option value="UMAR BABURA ROAD">UMAR BABURA ROAD</option>
        <option value="UMAR BABURA ROAD">UMAR BABURA ROAD</option>
        <option value="UMAR DANAZUMI ST">UMAR DANAZUMI ST</option>
        <option value="UMAR GALADIMA RD">UMAR GALADIMA RD</option>
        <option value="UMAR GIYE AV">UMAR GIYE AV</option>
        <option value="UMAR YAHAYA BANKAURA ST">UMAR YAHAYA BANKAURA ST</option>
        <option value="UMAR YAKUBU DANHASSAN RD">UMAR YAKUBU DANHASSAN RD</option>
        <option value="UNGOGO RD">UNGOGO RD</option>
        <option value="UNGUWAN GEZA RD">UNGUWAN GEZA RD</option>
        <option value="UNGUWAN GOJI RD">UNGUWAN GOJI RD</option>
        <option value="UNGUWAR GANO ST">UNGUWAR GANO ST</option>
        <option value="UNITY RD">UNITY RD</option>
        <option value="USMAN YASHIYA LINK">USMAN YASHIYA LINK</option>
        <option value="UTAI ST">UTAI ST</option>
        <option value="V.I.O LINE">V.I.O LINE</option>
        <option value="WADA RIRIWAI LINE">WADA RIRIWAI LINE</option>
        <option value="WAFF RD">WAFF RD</option>
        <option value="WARRI RD">WARRI RD</option>
        <option value="WASHIR HOSPITAL ROAD">WASHIR HOSPITAL ROAD</option>
        <option value="WATARI ST">WATARI ST</option>
        <option value="WEATHER HEAD RD">WEATHER HEAD RD</option>
        <option value="WEATHER HEAD ST">WEATHER HEAD ST</option>
        <option value="WUDIL ROAD">WUDIL ROAD</option>
        <option value="Y. MAITAMA SULE AV">Y. MAITAMA SULE AV</option>
        <option value="YAHAYA GUSAU ROAD">YAHAYA GUSAU ROAD</option>
        <option value="YAHAYA MUH'D AV">YAHAYA MUH'D AV</option>
        <option value="YAHAYA RD">YAHAYA RD</option>
        <option value="YAKUBU BAFFA RD">YAKUBU BAFFA RD</option>
        <option value="YAKUBU BAKO CL">YAKUBU BAKO CL</option>
        <option value="YAKUBU SHENDAM AV">YAKUBU SHENDAM AV</option>
        <option value="YAN AZARA ST">YAN AZARA ST</option>
        <option value="YAN AZARE RD">YAN AZARE RD</option>
        <option value="YAN SMOGLE ROAD">YAN SMOGLE ROAD</option>
        <option value="YAN TSIRE ROAD">YAN TSIRE ROAD</option>
        <option value="YANAWAKI  ROAD">YANAWAKI  ROAD</option>
        <option value="YANDUTSE ROAD">YANDUTSE ROAD</option>
        <option value="YANKABA COURT ROAD">YANKABA COURT ROAD</option>
        <option value="YANKAJI BUSTOP ROAD">YANKAJI BUSTOP ROAD</option>
        <option value="YANKARI AV">YANKARI AV</option>
        <option value="YANKATAKO RD">YANKATAKO RD</option>
        <option value="YANKATAKO ST">YANKATAKO ST</option>
        <option value="YANTSAKI ROAD">YANTSAKI ROAD</option>
        <option value="YARDIN ST">YARDIN ST</option>
        <option value="YARGAYA ST">YARGAYA ST</option>
        <option value="YAU ABDULLAHI YANSHANA CLOSE">YAU ABDULLAHI YANSHANA CLOSE</option>
        <option value="YAUTAI LINK">YAUTAI LINK</option>
        <option value="YOLA RD">YOLA RD</option>
        <option value="YOLA STREET">YOLA STREET</option>
        <option value="YOLAWA LINK">YOLAWA LINK</option>
        <option value="YOLAWA RD">YOLAWA RD</option>
        <option value="YORUBA RD">YORUBA RD</option>
        <option value="YORUBA RD">YORUBA RD</option>
        <option value="YUSUF RD">YUSUF RD</option>
        <option value="ZAKI ST">ZAKI ST</option>
        <option value="ZANGO BAREBERI ROAD">ZANGO BAREBERI ROAD</option>
        <option value="ZARIA ROAD">ZARIA ROAD</option>
        <option value="ZARIA ROAD">ZARIA ROAD</option>
        <option value="ZARIA ROAD">ZARIA ROAD</option>
        <option value="ZARIA ROAD">ZARIA ROAD</option>
        <option value="ZARIA ROAD">ZARIA ROAD</option>
        <option value="ZAWACHIKI AV">ZAWACHIKI AV</option>
        <option value="ZEREWA AV">ZEREWA AV</option>
        <option value="ZEREWA AV">ZEREWA AV</option>
        <option value="ZOO ROAD">ZOO ROAD</option>
        <option value="ZOO ROAD">ZOO ROAD</option>
        <option value="ZUBAIRU INUWA LINK">ZUBAIRU INUWA LINK</option>
        <option value="ZUMA ST">ZUMA ST</option>
        <option value="ZUNGERU ROAD">ZUNGERU ROAD</option>
        <option value="ZUWA ST">ZUWA ST</option>
          <option value="other">Other</option>
                            </select>
                            <input 
                                type="text" 
                                id="otherStreetName" 
                                x-show="showOtherStreet" 
                                x-model="customStreet" 
                                class="form-input text-sm property-input mt-2" 
                                placeholder="Please specify other street name"
                                x-transition
                                @input="handleCustomStreetInput($event.target.value)"
                            >
                        </div>
                        
                        <!-- District Component -->
                        <div class="space-y-2">
                            <label for="district" class="text-xs text-gray-600">District Name</label>
                            <select id="district" class="form-input text-sm property-input" 
                                    x-model="selectedDistrict"
                                    @change="handleDistrictChange($event.target.value)"
                                    name="district">
                                <option value="" selected>Select District Name</option>
                                <option value="DALA">DALA</option>
        <option value="DAWAKIN KUDU">DAWAKIN KUDU</option>
        <option value="FAGGE">FAGGE</option>
        <option value="GWALE">GWALE</option>
        <option value="KUMBOTSO">KUMBOTSO</option>
        <option value="AJINGI">AJINGI</option>
        <option value="ALBASU">ALBASU</option>
        <option value="BAGWAI">BAGWAI</option>
        <option value="BEBEJI">BEBEJI</option>
        <option value="BICHI">BICHI</option>
        <option value="BUNKURE">BUNKURE</option>
        <option value="CITY">CITY</option>
        <option value="CITY DISTRICT">CITY DISTRICT</option>
        <option value="D/KUDU">D/KUDU</option>
        <option value="DAMBATTA">DAMBATTA</option>
        <option value="DAN DINSHE KOFAR DAWANAU">DAN DINSHE KOFAR DAWANAU</option>
        <option value="DANBATTA">DANBATTA</option>
        <option value="DAWAKIL KUDU">DAWAKIL KUDU</option>
        <option value="DAWAKIN KUDU DISTRICT">DAWAKIN KUDU DISTRICT</option>
        <option value="DAWAKIN TOFA">DAWAKIN TOFA</option>
        <option value="DAWAKIN-KUDU">DAWAKIN-KUDU</option>
        <option value="DAWAKIN-TOFA">DAWAKIN-TOFA</option>
        <option value="DAWANAU TOFA">DAWANAU TOFA</option>
        <option value="DOGUWA">DOGUWA</option>
        <option value="DORAYI KARAMA">DORAYI KARAMA</option>
        <option value="GABASAWA">GABASAWA</option>
        <option value="GARKO">GARKO</option>
        <option value="GARUN MALAM">GARUN MALAM</option>
        <option value="GARUN MALLAM">GARUN MALLAM</option>
        <option value="GAYA">GAYA</option>
        <option value="GEZAWA">GEZAWA</option>
        <option value="GWALA">GWALA</option>
        <option value="GWALE DISTRICT">GWALE DISTRICT</option>
        <option value="GWAMMAJA">GWAMMAJA</option>
        <option value="GWARZO">GWARZO</option>
        <option value="HAUSAWA">HAUSAWA</option>
        <option value="INUBAWA">INUBAWA</option>
        <option value="KABO">KABO</option>
        <option value="KANO CITY">KANO CITY</option>
        <option value="KANO MUNICIPAL">KANO MUNICIPAL</option>
        <option value="KANO MUNICIPAL CITY">KANO MUNICIPAL CITY</option>
        <option value="KANO STATE">KANO STATE</option>
        <option value="KANO-CITY">KANO-CITY</option>
        <option value="KARAYE">KARAYE</option>
        <option value="KIBIYA">KIBIYA</option>
        <option value="KIMBOTSO">KIMBOTSO</option>
        <option value="KIRU">KIRU</option>
        <option value="KOFAR DAWANAU">KOFAR DAWANAU</option>
        <option value="KUMBOSTO">KUMBOSTO</option>
        <option value="KUMBOTSO VILLAGE">KUMBOTSO VILLAGE</option>
        <option value="KUMBOTSOI">KUMBOTSOI</option>
        <option value="KUNCHI">KUNCHI</option>
        <option value="KURA">KURA</option>
        <option value="MADOBI">MADOBI</option>
        <option value="MAKODA">MAKODA</option>
        <option value="MINJIBIR">MINJIBIR</option>
        <option value="MUNICIPAL">MUNICIPAL</option>
        <option value="MUNICIPAL LOCAL GOVERNMENT">MUNICIPAL LOCAL GOVERNMENT</option>
        <option value="MUNNICIPAL">MUNNICIPAL</option>
        <option value="NASARAWA">NASARAWA</option>
        <option value="NASSARAWA">NASSARAWA</option>
        <option value="RANO">RANO</option>
        <option value="RIMIN GADO">RIMIN GADO</option>
        <option value="RIMIN ZAKARA">RIMIN ZAKARA</option>
        <option value="ROGO">ROGO</option>
        <option value="SUMAILA">SUMAILA</option>
        <option value="TAKAI">TAKAI</option>
        <option value="TARAUNI">TARAUNI</option>
        <option value="TARAUNI DISTRICT">TARAUNI DISTRICT</option>
        <option value="TOFA">TOFA</option>
        <option value="TSANTAWA">TSANTAWA</option>
        <option value="TSANYAWA">TSANYAWA</option>
        <option value="TUDUN WADA">TUDUN WADA</option>
        <option value="UNGOGGO">UNGOGGO</option>
        <option value="UNGOGO">UNGOGO</option>
        <option value="WAJE">WAJE</option>
        <option value="WARAWA">WARAWA</option>
        <option value="WUDIL">WUDIL</option>
        <option value="ZAWACHIKI">ZAWACHIKI</option>
        <option value="other">Other</option>
                            </select>
                            <input 
                                type="text" 
                                id="otherDistrict" 
                                x-show="showOtherDistrict" 
                                x-model="customDistrict" 
                                class="form-input text-sm property-input mt-2" 
                                placeholder="Please specify other district name"
                                x-transition
                                @input="handleCustomDistrictInput($event.target.value)"
                            >
                        </div>
                    </div>
                    <div>
                        <label for="lga" class="text-xs text-gray-600">LGA</label>
                        <select id="lga" name="lgsaOrCity" x-model="lga" class="form-input text-sm property-input">
                            <option value="">Select LGA</option>
                             <option value="Ajingi">Ajingi</option>
                            <option value="Albasu">Albasu</option>
                            <option value="Bagwai">Bagwai</option>
                            <option value="Bebeji">Bebeji</option>
                            <option value="Bichi">Bichi</option>
                            <option value="Bunkure">Bunkure</option>
                            <option value="Dala">Dala</option>
                            <option value="Dambatta">Dambatta</option>
                            <option value="Dawakin Kudu">Dawakin Kudu</option>
                            <option value="Dawakin Tofa">Dawakin Tofa</option>
                            <option value="Doguwa">Doguwa</option>
                            <option value="Fagge">Fagge</option>
                            <option value="Gabasawa">Gabasawa</option>
                            <option value="Garko">Garko</option>
                            <option value="Garun Mallam">Garun Mallam</option>
                            <option value="Gaya">Gaya</option>
                            <option value="Gezawa">Gezawa</option>
                            <option value="Gwale">Gwale</option>
                            <option value="Gwarzo">Gwarzo</option>
                            <option value="Kabo">Kabo</option>
                            <option value="Kano Municipal">Kano Municipal</option>
                            <option value="Karaye">Karaye</option>
                            <option value="Kibiya">Kibiya</option>
                            <option value="Kiru">Kiru</option>
                            <option value="Kumbotso">Kumbotso</option>
                            <option value="Kunchi">Kunchi</option>
                            <option value="Kura">Kura</option>
                            <option value="Madobi">Madobi</option>
                            <option value="Makoda">Makoda</option>
                            <option value="Minjibir">Minjibir</option>
                            <option value="Nasarawa">Nasarawa</option>
                            <option value="Rano">Rano</option>
                            <option value="Rimin Gado">Rimin Gado</option>
                            <option value="Rogo">Rogo</option>
                            <option value="Shanono">Shanono</option>
                            <option value="Sumaila">Sumaila</option>
                            <option value="Takai">Takai</option>
                            <option value="Tarauni">Tarauni</option>
                            <option value="Tofa">Tofa</option>
                            <option value="Tsanyawa">Tsanyawa</option>
                            <option value="Tudun Wada">Tudun Wada</option>
                            <option value="Ungogo">Ungogo</option>
                            <option value="Warawa">Warawa</option>
                            <option value="Wudil">Wudil</option>
                        </select>
                    </div>
                     
                    <!-- State -->
                    <div>
                        <label for="state" class="text-xs text-gray-600">State</label>
                        <input id="state" name="state" x-model="state"  type="text" class="form-input text-sm property-input" placeholder="Enter state">
                    </div>
                </div>
            </div>
        </div>

        <!-- Instrument Type Section -->
        <div class="form-section">
            <h4 class="form-section-title">Instrument Type</h4>
            <div class="space-y-3">
                <!-- Transaction Type and Date -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label for="transactionType-record" class="text-sm">Transaction Type</label>
                        <select id="transactionType-record" name="transactionType" x-model="selectedTransactionType" class="form-select text-sm transaction-type-select">
                            <option value="">Select type</option>
                            <option value="Deed of Transfer">Deed of Transfer</option>
                            <option value="Certificate of Occupancy">Certificate of Occupancy</option>
                            <option value="ST Certificate of Occupancy">ST Certificate of Occupancy</option>
                            <option value="SLTR Certificate of Occupancy">SLTR Certificate of Occupancy</option>
                            <option value="Irrevocable Power of Attorney">Irrevocable Power of Attorney</option>
                            <option value="Deed of Release">Deed of Release</option>
                            <option value="Deed of Assignment">Deed of Assignment</option>
                            <option value="ST Assignment">ST Assignment</option>
                            <option value="Deed of Mortgage">Deed of Mortgage</option>
                            <option value="Tripartite Mortgage">Tripartite Mortgage</option>
                            <option value="Deed of Sub Lease">Deed of Sub Lease</option>
                            <option value="Deed of Sub Under Lease">Deed of Sub Under Lease</option>
                            <option value="Power of Attorney">Power of Attorney</option>
                            <option value="Deed of Surrender">Deed of Surrender</option>
                            <option value="Indenture of Lease">Indenture of Lease</option>
                            <option value="Deed of Variation">Deed of Variation</option>
                            <option value="Customary Right of Occupancy">Customary Right of Occupancy</option>
                            <option value="Vesting Assent">Vesting Assent</option>
                            <option value="Court Judgement">Court Judgement</option>
                            <option value="Exchange of Letters">Exchange of Letters</option>
                            <option value="Tenancy Agreement">Tenancy Agreement</option>
                            <option value="Revocation of Power of Attorney">Revocation of Power of Attorney</option>
                            <option value="Deed of Convenyence">Deed of Convenyence</option>
                            <option value="Memorandom of Agreement">Memorandom of Agreement</option>
                            <option value="Quarry Lease">Quarry Lease</option>
                            <option value="Private Lease">Private Lease</option>
                            <option value="Deed of Gift">Deed of Gift</option>
                            <option value="Deed of Partition">Deed of Partition</option>
                            <option value="Non-European Occupational Lease">Non-European Occupational Lease</option>
                            <option value="Deed of Revocation">Deed of Revocation</option>
                            <option value="Deed of lease">Deed of lease</option>
                            <option value="Deed of Reconveyance">Deed of Reconveyance</option>
                            <option value="Letter of Administration">Letter of Administration</option>
                            <option value="Customary Inhertitance">Customary Inhertitance</option>
                            <option value="Certificate of Purchase">Certificate of Purchase</option>
                            <option value="Deed of Rectification">Deed of Rectification</option>
                            <option value="Building Lease">Building Lease</option>
                            <option value="Memorandum of Loss">Memorandum of Loss</option>
                            <option value="Vesting Deed">Vesting Deed</option>
                            <option value="ST Fragmentation">ST Fragmentation</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label for="transactionDate" class="text-sm">Transaction/Certificate Date</label>
                        <input type="date" id="transactionDate" name="transactionDate" class="form-input text-sm">
                    </div>
                </div>

                <!-- Registration Number Components -->
                <div class="space-y-1" x-data="{ serialNo: '', pageNo: '', volumeNo: '', showPreview: false, get regNoDisplay() { return [this.serialNo, this.pageNo, this.volumeNo].filter(Boolean).join('/') || 'Not set'; } }">
                    <label class="text-sm">Registration Number</label>
                    <div class="grid grid-cols-5 gap-2">
                        <div>
                            <label for="serialNo" class="text-xs">Serial No.</label>
                            <input id="serialNo" name="serialNo" x-model="serialNo" @input="showPreview = serialNo || pageNo || volumeNo" class="form-input text-xs py-1" placeholder="e.g. 1">
                        </div>
                        <div>
                            <label for="pageNo" class="text-xs">Page No.</label>
                            <input id="pageNo" name="pageNo" x-model="pageNo" @input="showPreview = serialNo || pageNo || volumeNo" class="form-input text-xs py-1" placeholder="e.g. 1">
                        </div>
                        <div>
                            <label for="volumeNo" class="text-xs">Volume No.</label>
                            <input id="volumeNo" name="volumeNo" x-model="volumeNo" @input="showPreview = serialNo || pageNo || volumeNo" class="form-input text-xs py-1" placeholder="e.g. 2">
                        </div>
                        <div>
                            <label for="regDate" class="text-xs">Reg Date</label>
                            <input id="regDate" name="regDate" type="date" class="form-input text-xs py-1">
                        </div>
                        <div>
                            <label for="regTime" class="text-xs">Reg Time</label>
                            <input id="regTime" name="regTime" type="time" class="form-input text-xs py-1">
                        </div>
                    </div>
                    <div x-show="showPreview" x-transition class="mt-2 p-3 bg-blue-50 border-2 border-blue-200 rounded-lg shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-semibold text-blue-700">Registration Number:</span>
                            </div>
                            <span class="text-lg font-bold text-blue-800 tracking-wider" x-text="regNoDisplay"></span>
                        </div>
                        <div class="mt-1.5 flex justify-between items-center">
                            <div class="text-xs text-blue-600">Format: Serial No/Page No/Volume No</div>
                            <div x-show="serialNo && pageNo && volumeNo" class="text-xs font-medium text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">Complete</div>
                        </div>
                    </div>
                </div>

                <!-- Land Use Type -->
                
                <div class="grid grid-cols-3 gap-3">
                    <div class="space-y-1">
                    <label for="landUse" class="text-sm">Land Use</label>
                    <select id="landUse" name="landUse" class="form-select text-sm">
                    <option value="">Select land use</option>
                    <option value="RESIDENTIAL">RESIDENTIAL</option>
                    <option value="AGRICULTURAL">AGRICULTURAL</option>
                    <option value="COMMERCIAL">COMMERCIAL</option>
                    <option value="COMMERCIAL ( WARE HOUSE)">COMMERCIAL ( WARE HOUSE)</option>
                    <option value="COMMERCIAL (OFFICES)">COMMERCIAL (OFFICES)</option>
                    <option value="COMMERCIAL (PETROL FILLING STATION)">COMMERCIAL (PETROL FILLING STATION)</option>
                    <option value="COMMERCIAL (RICE PROCESSING)">COMMERCIAL (RICE PROCESSING)</option>
                    <option value="COMMERCIAL (SCHOOL)">COMMERCIAL (SCHOOL)</option>
                    <option value="COMMERCIAL (SHOPS & PUBLIC CONVINIENCE)">COMMERCIAL (SHOPS & PUBLIC CONVINIENCE)</option>
                    <option value="COMMERCIAL (SHOPS AND OFFICES)">COMMERCIAL (SHOPS AND OFFICES)</option>
                    <option value="COMMERCIAL (SHOPS)">COMMERCIAL (SHOPS)</option>
                    <option value="COMMERCIAL (WAREHOUSE)">COMMERCIAL (WAREHOUSE)</option>
                    <option value="COMMERCIAL (WORKSHOP AND OFFICES)">COMMERCIAL (WORKSHOP AND OFFICES)</option>
                    <option value="COMMERCIAL AND RESIDENTIAL">COMMERCIAL AND RESIDENTIAL</option>
                    <option value="INDUSTRIAL">INDUSTRIAL</option>
                    <option value="INDUSTRIAL (SMALL SCALE)">INDUSTRIAL (SMALL SCALE)</option>
                    <option value="RESIDENTIAL AND COMMERCIAL">RESIDENTIAL AND COMMERCIAL</option>
                    <option value="RESIDENTIAL/COMMERCIAL">RESIDENTIAL/COMMERCIAL</option>
                    <option value="RESIDENTIAL/COMMERCIAL LAYOUT">RESIDENTIAL/COMMERCIAL LAYOUT</option>
                </select>
                    </div>
                    <div class="space-y-1">
                        <label for="period" class="text-sm">Period</label>
                        <input id="period" name="period" type="number" class="form-input text-sm" placeholder="e.g. 99">
                    </div>
                    <div class="space-y-1">
                        <label for="periodUnit" class="text-sm">Period Unit</label>
                        <select id="periodUnit" name="periodUnit" class="form-select text-sm">
                            <option value="">Select unit</option>
                            <option value="YEARS">YEARS</option>
                            <option value="MONTHS">MONTHS</option>
                            <option value="DAYS">DAYS</option>
                            <option value="PERPETUAL">PERPETUAL</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Specific Fields -->
        <div id="transaction-specific-fields-record" class="form-section" x-show="selectedTransactionType" x-transition>
            <h4 class="form-section-title">Transaction Details</h4>
            
            <!-- Default/Grant fields -->
            <div id="default-fields-record" class="transaction-fields" x-show="shouldShowDefaultFields" x-transition>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label for="grantor-record" class="text-sm" x-text="partyLabels.firstParty"></label>
                        <input id="grantor-record" name="Grantor" class="form-input text-sm" :placeholder="`Enter ${partyLabels.firstParty.toLowerCase()} name`" :value="autoFilledGrantor" :readonly="isGrantorReadonly" :class="isGrantorReadonly ? 'bg-gray-100' : ''">
                    </div>
                    <div class="space-y-1">
                        <label for="grantee-record" class="text-sm" x-text="partyLabels.secondParty"></label>
                        <input id="grantee-record" name="Grantee" class="form-input text-sm" :placeholder="`Enter ${partyLabels.secondParty.toLowerCase()} name`">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-sm"> </label>
            <textarea id="property-description" name="property_description" rows="4" class="form-input text-sm" readonly x-text="description"></textarea>
            <div class="text-xs text-gray-500 italic">This field is auto-populated based on property details</div>
        </div>
    </div>

    <div class="flex justify-end space-x-3 pt-2 border-t mt-4">
        <button id="property-submit-btn" type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>

@if(!$is_ai)
    </div>
</div>
@endif

<script>
// Alpine.js component for Property Record Form
function propertyRecordForm() {
    return {
        selectedTransactionType: '',

        // Property description variables
        house: '',
        plot: '',
        street: '',
        district: '',
        lga: '',
        state: 'Kano',

        // Component state variables
        selectedStreet: '',
        selectedDistrict: '',
        showOtherStreet: false,
        customStreet: '',
        showOtherDistrict: false,
        customDistrict: '',

        // Computed property for description
        get description() {
            let desc = '';
            if (this.house) desc += ` ${this.house}`;
            if (this.street) desc += (desc ? ' ' : '') + this.street;
            if (this.district) desc += (desc ? ' ' : '') + this.district;
            if (this.lga) desc += (desc ? ' ' : '') + `${this.lga} LGA`;
            if (this.state) desc += (desc ? ' ' : '') + this.state;
            return desc;
        },

        // Handle street changes
        handleStreetChange(value) {
            if (value === 'other') {
                this.showOtherStreet = true;
                this.street = this.customStreet;
            } else if (value && value !== 'other') {
                this.showOtherStreet = false;
                this.customStreet = '';
                this.street = value;
            } else {
                // Custom input value
                this.street = value;
                this.customStreet = value;
            }
        },

        // Handle district changes
        handleDistrictChange(value) {
            if (value === 'other') {
                this.showOtherDistrict = true;
                this.district = this.customDistrict;
            } else if (value && value !== 'other') {
                this.showOtherDistrict = false;
                this.customDistrict = '';
                this.district = value;
            } else {
                // Custom input value
                this.district = value;
                this.customDistrict = value;
            }
        },

        // Handle custom street input
        handleCustomStreetInput(value) {
            this.street = value;
        },

        // Handle custom district input
        handleCustomDistrictInput(value) {
            this.district = value;
        },

        // Define transaction types with their corresponding party labels
        transactionTypes: {
            'Deed of Transfer': { firstParty: 'Transferor', secondParty: 'Transferee' },
            'Certificate of Occupancy': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'ST Certificate of Occupancy': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'SLTR Certificate of Occupancy': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Irrevocable Power of Attorney': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Deed of Release': { firstParty: 'Releasor', secondParty: 'Releasee' },
            'Deed of Assignment': { firstParty: 'Assignor', secondParty: 'Assignee' },
            'ST Assignment': { firstParty: 'Assignor', secondParty: 'Assignee' },
            'Deed of Mortgage': { firstParty: 'Mortgagor', secondParty: 'Mortgagee' },
            'Tripartite Mortgage': { firstParty: 'Mortgagor', secondParty: 'Mortgagee' },
            'Deed of Sub Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Deed of Sub Under Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Power of Attorney': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Deed of Surrender': { firstParty: 'Surrenderor', secondParty: 'Surrenderee' },
            'Indenture of Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Deed of Variation': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Customary Right of Occupancy': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Vesting Assent': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Court Judgement': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Exchange of Letters': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Tenancy Agreement': { firstParty: 'Landlord', secondParty: 'Tenant' },
            'Revocation of Power of Attorney': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Deed of Convenyence': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Memorandom of Agreement': { firstParty: 'First Party', secondParty: 'Second Party' },
            'Quarry Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Private Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Deed of Gift': { firstParty: 'Donor', secondParty: 'Donee' },
            'Deed of Partition': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Non-European Occupational Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Deed of Revocation': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Deed of lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Deed of Reconveyance': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Letter of Administration': { firstParty: 'Administrator', secondParty: 'Beneficiary' },
            'Customary Inhertitance': { firstParty: 'Grantor', secondParty: 'Heir' },
            'Certificate of Purchase': { firstParty: 'Vendor', secondParty: 'Purchaser' },
            'Deed of Rectification': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Building Lease': { firstParty: 'Lessor', secondParty: 'Lessee' },
            'Memorandum of Loss': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Vesting Deed': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'ST Fragmentation': { firstParty: 'Grantor', secondParty: 'Grantee' },
            'Other': { firstParty: 'Grantor', secondParty: 'Grantee' }
        },

        // Computed property for party labels
        get partyLabels() {
            if (this.selectedTransactionType && this.transactionTypes[this.selectedTransactionType]) {
                return this.transactionTypes[this.selectedTransactionType];
            }
            return { firstParty: 'Grantor', secondParty: 'Grantee' };
        },

        // Computed property to determine if default fields should be shown
        get shouldShowDefaultFields() {
            const specificTypes = ['Assignment', 'Mortgage', 'Surrender', 'Sub-Lease'];
            return this.selectedTransactionType && !specificTypes.includes(this.selectedTransactionType);
        },

        // Computed property for auto-filled grantor
        get autoFilledGrantor() {
            if (this.selectedTransactionType === 'Certificate of Occupancy' || this.selectedTransactionType === 'ST Certificate of Occupancy' || this.selectedTransactionType === 'SLTR Certificate of Occupancy' || this.selectedTransactionType === 'Customary Right of Occupancy') {
                return 'KANO STATE GOVERNMENT';
            }
            return '';
        },

        // Computed property for grantor readonly state
        get isGrantorReadonly() {
            return this.selectedTransactionType === 'Certificate of Occupancy' || this.selectedTransactionType === 'ST Certificate of Occupancy' || this.selectedTransactionType === 'SLTR Certificate of Occupancy' || this.selectedTransactionType === 'Customary Right of Occupancy';
        },

        // Initialize the component
        init() {
            console.log('🚀 Alpine.js Property Record Form initialized');

            // Watch for changes in selectedTransactionType
            this.$watch('selectedTransactionType', (value) => {
                console.log('🔄 Transaction type changed to:', value);
            });

            // Watch for changes in description
            this.$watch('description', (value) => {
                console.log('🔄 Description updated:', value);
            });

            // Add form submission handler
            this.$nextTick(() => {
                const form = document.getElementById('property-record-form');
                if (form) {
                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.submitForm();
                    });
                }
            });
        },

        // Handle form submission with SweetAlert
        submitForm() {
            const form = document.getElementById('property-record-form');
            const formData = new FormData(form);

            // Show loading
            Swal.fire({
                title: 'Submitting...',
                text: 'Please wait while we save your property record',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit form via fetch
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Property record created successfully',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Reset form and close dialog
                        form.reset();
                        this.resetFormData();
                        
                        // Close dialog if it exists
                        const dialog = document.getElementById('property-form-dialog');
                        if (dialog) {
                            dialog.classList.add('hidden');
                        }
                        
                        // Reload page to show new record
                        window.location.reload();
                    });
                } else {
                    // Handle validation errors
                    let errorMessage = data.message || 'An error occurred';
                    if (data.errors) {
                        const errorList = Object.values(data.errors).flat();
                        errorMessage = errorList.join('\n');
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred. Please try again.',
                    confirmButtonText: 'OK'
                });
            });
        },

        // Reset form data
        resetFormData() {
            this.selectedTransactionType = '';
            this.house = '';
            this.plot = '';
            this.street = '';
            this.district = '';
            this.lga = '';
            this.state = 'Kano';
            this.selectedStreet = '';
            this.selectedDistrict = '';
            this.showOtherStreet = false;
            this.customStreet = '';
            this.showOtherDistrict = false;
            this.customDistrict = '';
        }
    }
}

console.log('🎉 Alpine.js Property Record Form script loaded');
</script>