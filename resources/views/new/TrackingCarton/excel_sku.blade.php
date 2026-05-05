   <table style="border-collapse: collapse; width: 100%;">
       <thead>
           <tr>
               <th colspan="6" style="text-align: center; font-weight: bold;border: 1px solid black;">INBOUND
                   TRANSACTION</th>
           </tr>
           <tr>
               {{-- <th style="text-align: center; vertical-align: middle;border: 1px solid black;">JOB NO</th> --}}
               <th style="text-align: center; vertical-align: middle;border: 1px solid black;">INBOUND DATE</th>
               <th style="text-align: center; vertical-align: middle;border: 1px solid black;">VEHICLE NO</th>
               <th style="text-align: center; vertical-align: middle;border: 1px solid black;">DESCRIPTION</th>
               <th style="text-align: center; vertical-align: middle;border: 1px solid black;">PRODUCT CODE</th>
               <th style="text-align: center; vertical-align: middle;border: 1px solid black;">QTY</th>
               <th style="text-align: center; vertical-align: middle;border: 1px solid black;">CARTON ID</th>
           </tr>
       </thead>
       <tbody>
           @foreach ($groupedEanCodes as $jobNo => $productsByJob)
               @foreach ($productsByJob as $productCode => $data)
                   @php
                       $ean_index = 1;
                   @endphp
                   <tr>
                       {{-- <td style="text-align: center; vertical-align: middle;border: 1px solid black;">{{ $data['job_no'] }}</td> --}}
                       <td style="text-align: center; vertical-align: middle;border: 1px solid black;">
                           {{ $data['created_at'] }}</td>
                       <td style="text-align: center; vertical-align: middle;border: 1px solid black;">
                           {{ $data['vehicle'] }}</td>
                       <td style="text-align: center; vertical-align: middle;border: 1px solid black;">
                           {{ $data['deskripsi'] }}</td>
                       <td style="text-align: center; vertical-align: middle;border: 1px solid black;">
                           {{ $data['product_code'] }}</td>
                       <td style="text-align: center; vertical-align: middle;border: 1px solid black;">
                           {{ $data['ean_count'] . ' ' . $data['puom'] }}</td>
                       <td style="min-width: 150px;">
                           <ol start="{{ $ean_index }}"
                               style="max-height: 150px; overflow-y: auto; padding-left: 20px; list-style-position: inside;">
                               @foreach ($data['ean_codes'] as $ean)
                                   <li>{{ $ean }}</li>
                                   @php $ean_index++; @endphp
                               @endforeach
                           </ol>
                       </td>
                   </tr>
               @endforeach
           @endforeach
       </tbody>
   </table>
   <br>
   <table style="border-collapse: collapse; width: 100%;">
       <thead>
           <tr>
               <th colspan="6" style="text-align: center; font-weight: bold;border: 1px solid black;">OUTBOUND
                   TRANSACTION</th>
           </tr>
           <tr>
               {{-- <th style="text-align: center; vertical-align: middle;border: 1px solid black;">JOB NO</th> --}}
               <th style="text-align: center; vertical-align: middle;border: 1px solid black;">JOB DATE</th>
               <th style="text-align: center; vertical-align: middle;border: 1px solid black;">VEHICLE NO</th>
               <th style="text-align: center; vertical-align: middle;border: 1px solid black;">CUSTOMER NAME</th>
               <th style="text-align: center; vertical-align: middle;border: 1px solid black;">PRODUCT CODE</th>
               <th style="text-align: center; vertical-align: middle;border: 1px solid black;">QTY</th>
               <th style="text-align: center; vertical-align: middle;border: 1px solid black;">CARTON ID</th>
           </tr>
       </thead>
       <tbody>
           @foreach ($groupedOutbound as $group)
               @foreach ($group['products'] as $product)
                   @php
                       $eanIndex = 1;
                   @endphp
                   <tr>
                       {{-- <td style="text-align: center; vertical-align: middle;border: 1px solid black;">{{ $group['job_no'] }}</td> --}}
                       <td style="text-align: center; vertical-align: middle;border: 1px solid black;">
                           {{ $group['job_date'] }}</td>
                       <td style="text-align: center; vertical-align: middle;border: 1px solid black;">
                           {{ $group['vehicle_no'] }}</td>
                       <td style="text-align: center; vertical-align: middle;border: 1px solid black;">
                           {{ $group['customer_name'] }}</td>
                       <td style="text-align: center; vertical-align: middle;border: 1px solid black;">
                           {{ $product['product_code'] }}</td>
                       <td style="text-align: center; vertical-align: middle;border: 1px solid black;">
                           {{ $product['ean_code_count'] . ' ' . $product['puom'] }}</td>
                       <td style="text-align: center; vertical-align: middle;border: 1px solid black;">
                           @if ($product['ean_codes']->isNotEmpty())
                               <ol start="{{ $eanIndex }}"
                                   style="max-height: 150px; overflow-y: auto; padding-left: 20px; list-style-position: inside;">
                                   @foreach ($product['ean_codes'] as $ean)
                                       <li>{{ $ean }}</li>
                                       @php $eanIndex++; @endphp
                                   @endforeach
                               </ol>
                           @else
                               <em>-</em>
                           @endif
                       </td>
                   </tr>
               @endforeach
           @endforeach
       </tbody>
   </table>
