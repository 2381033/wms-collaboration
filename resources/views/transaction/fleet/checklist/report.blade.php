<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="{{ asset('assets/css/portrait_check.css') }}">
</head>

<body>
    <div class="page">
        <div class="header">
            <img alt="image" class="mr-3 logo" src="{{ asset('images/logos.png') }}" />
        </div>
        <table class="table-template">
            <thead>
                <tr>
                    <td>
                        <div class="header-space">&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="title">
                            <h3 class="title-header">
                                Pemeriksaan Kelayakan & Perlengkapan Truck
                            </h3>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="content">
                            <table class="table-header-kerangka">
                                <tr>
                                    <td>
                                        <table class="table-header" style="width: 100%;">
                                            <tr>
                                                <td>Nomor Dokumen</td>
                                                <td>:</td>
                                                <td>{{ $header->job_no }}</td>
                                                <td>Tgl Dokumen</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($header->job_date)->format('d/m/Y') }}</td>
                                                <td>No Seal</td>
                                                <td>:</td>
                                                <td>{{ $header->seal_no }}</td>
                                            </tr>
                                            <tr>
                                                <td>Jenis Armada</td>
                                                <td>:</td>
                                                <td>{{ $header->size_name }}</td>
                                                <td>Tipe Kontainer</td>
                                                <td>:</td>
                                                <td>{{ $header->type_name }}</td>
                                                <td>No Kontainer</td>
                                                <td>:</td>
                                                <td>{{ $header->container_no }}</td>
                                            </tr>
                                            <tr>
                                                <td>Nomor Polisi</td>
                                                <td>:</td>
                                                <td>{{ $header->vehicle_no }}</td>
                                                <td>Nama Supir</td>
                                                <td>:</td>
                                                <td>{{ $header->driver_name }}</td>
                                                <td>Nomor HP</td>
                                                <td>:</td>
                                                <td>{{ $header->phone_no }}</td>
                                            </tr>
                                            <tr>
                                                <td>Keterangan</td>
                                                <td>:</td>
                                                <td colspan="7">{{ $header->remarks }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr class="center">
                                        <th>No.</th>
                                        <th>Kegiatan</th>
                                        <th>Hasil Pemeriksaan</th>
                                        <th>Catatan / Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $group_name = '';
                                        $i = 1;
                                    @endphp
                                    @foreach ($detail as $item)
                                        @if ($group_name !== $item->group_name)
                                            @php
                                                $i = 1;
                                            @endphp
                                            <tr>
                                                <td colspan="4"><b>{{ $item->group_name }}</b></td>
                                            </tr>
                                        @endif
                                        @php
                                            $id = $item->id - 1;
                                        @endphp
                                        <tr>
                                            <td>{{ $id }}</td>
                                            <td>{{ $item->item_name }}</td>
                                            <td>
                                                {{-- <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="yes" value="Yes" {{ $item->results_flag == 'Yes' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="yes">Ya</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" id="no" value="No" {{ $item->results_flag == 'No' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="no">Tidak</label>
                                                </div> --}}
                                                {{ $item->results_flag }}
                                            </td>
                                            @if ($item->item_type == 'Remarks')
                                                <td>
                                                    {{ $item->remarks }}
                                                </td>
                                            @else
                                                <td>
                                                    @switch($item->action_flag)
                                                        @case('Proper')
                                                            Layak
                                                        @break

                                                        @case('Lesss')
                                                            Tolak
                                                        @break

                                                        @default
                                                            Peringatan
                                                    @endswitch
                                                </td>
                                                {{-- <td>                                                    
                                                    <input type="hidden" class="form-control" name="remarks[]">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="actionFlag[{{$id}}]" id="proper" value="Proper" {{ $item->action_flag == 'Proper' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="proper">Layak</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="actionFlag[{{$id}}]" id="less" value="Less" {{ $item->action_flag == 'Less' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="less">Tolak</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="actionFlag[{{$id}}]" id="alert" value="Alert" {{ $item->action_flag == 'Alert' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="less">Peringatan</label>
                                                    </div>
                                                </td> --}}
                                            @endif
                                        </tr>
                                        @php
                                            $group_name = $item->group_name;
                                            $i++;
                                        @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <div class="footer-space">&nbsp;</div>
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="signature">
            <table class="table">
                <tr>
                    <td class="sign">Driver Name:</td>
                    <td class="sign">Security:</td>
                </tr>
                <tr>
                    <td style="height: 30px;">Signature:</td>
                    <td>Signature:</td>
                </tr>
            </table>
        </div>
        <div class="footer">
            Print Date : {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}, Print By {{ Auth::user()->username }}
        </div>
    </div>
</body>

</html>
