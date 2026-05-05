@component('mail::message')
# Inbound Notification

Your message :

@component('mail::table')
| Job No  | Job Date | Description | ETA  |
|:------- |:---------|:------------|:-----|
@foreach ($data as $item)
|{{$item->job_no}}|{{\Carbon\Carbon::parse($item->job_date)->format("d-m-Y")}}|{{$item->description}}|{{\Carbon\Carbon::parse($item->eta)->format("d-m-Y")}}|
@endforeach
@endcomponent

<br>
Thanks,<br>
Administrator<br>
PT Masaji Kargasentra Tama
@endcomponent