 <table style="border-collapse: collapse; width: 100%;">
     <thead>
         <tr>
             <th colspan="6" style="text-align: center; font-weight: bold;border: 1px solid black;">INBOUND TRANSACTION
             </th>
         </tr>
         <tr>
             <th style="text-align: center; vertical-align: middle; border: 1px solid black;">JOB NO</th>
             <th style="text-align: center; vertical-align: middle; border: 1px solid black;">INBOUND DATE</th>
             <th style="text-align: center; vertical-align: middle; border: 1px solid black;">VEHICLE NO</th>
             <th style="text-align: center; vertical-align: middle; border: 1px solid black;">DESCRIPTION</th>
             <th style="text-align: center; vertical-align: middle; border: 1px solid black;">PRODUCT CODE</th>
             <th style="text-align: center; vertical-align: middle; border: 1px solid black;">CARTON ID</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($filteredInbound as $value)
             <tr>
                 <td style="text-align: center; vertical-align: middle; border: 1px solid black;">{{ $value->job_no }}
                 </td>
                 <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                     {{ $value->confirmed_date }}</td>
                 <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                     {{ $value->vehicle_no }}</td>
                 <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                     {{ $value->description }}</td>
                 <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                     {{ $value->product_code }}</td>
                 <td style="text-align: center; vertical-align: middle; border: 1px solid black;">{{ $value->ean_code }}
                 </td>
             </tr>
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
             <th style="text-align: center; vertical-align: middle; border: 1px solid black;">JOB NO</th>
             <th style="text-align: center; vertical-align: middle; border: 1px solid black;">JOB DATE</th>
             <th style="text-align: center; vertical-align: middle; border: 1px solid black;">VEHICLE NO</th>
             <th style="text-align: center; vertical-align: middle; border: 1px solid black;">CUSTOMER NAME</th>
             <th style="text-align: center; vertical-align: middle; border: 1px solid black;">PRODUCT CODE</th>
             <th style="text-align: center; vertical-align: middle; border: 1px solid black;">CARTON ID</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($filteredOutbound as $group)
             <tr>
                 <td style="text-align: center; vertical-align: middle; border: 1px solid black;">{{ $group->job_no }}
                 </td>
                 <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                     {{ $group->confirmed_date }}</td>
                 <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                     {{ $group->vehicle_no }}</td>
                 <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                     {{ $group->customer_name }}</td>
                 <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                     {{ $group->product_code }}</td>
                 <td style="text-align: center; vertical-align: middle; border: 1px solid black;">
                     {{ $group->ean_code }}</td>
             </tr>
         @endforeach
     </tbody>
 </table>
