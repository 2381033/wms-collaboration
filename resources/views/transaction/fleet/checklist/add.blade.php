@extends('layouts.main')

@section('title')
    Fleet CheckList
@endsection

@section('content')    
    <section id="breadcrumbs" class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Fleet CheckList</h2>
                <ol>
                    <li><a href="{{route('home')}}">Home</a></li>
                    <li>Fleet CheckList</li>
                </ol>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="branch_id">Branch name</label>
                        <form id="form-job" name="form-job" method="post">
                            @csrf
                            <select name="branch_id" id="branch_id" class="custom-select">
                                @foreach (Auth::user()->branch as $item)                         
                                    <option value="{{$item->id}}">{{$item->branch_name}}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <button type="button" onclick="createJob('Inbound');" class="btn btn-info btn-sm">Create Inbound</button>
                    <button type="button" onclick="createJob('Outbound');" class="btn btn-warning btn-sm">Create Outbound</button>                
                </div>
            </div>
        </div>
    </section> 
@endsection

@section('modal')
    
@endsection

@push('scripts')
<script>
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    function createJob(job_type) {
        $('.hidden-job').remove();
        $('#form-job').append(
            $('<input>')
                .attr('type', 'hidden')
                .attr('name', "job_type")
                .attr('class', 'hidden-job')
                .val(job_type)
        );
        
        $.ajax({       
            data: $('#form-job').serialize(),     
            url: "{{ route('fleet-checklist.create') }}",
            type: "POST",
            dataType: 'json',
            beforeSend: function () {
                $("#loader").show();
            },
            success: function (data) {                   
                $("#loader").hide();
                if($.isEmptyObject(data.error)){
                    swal({
                        icon: "success",
                        text: "Data Successfully Saved."                    
                    });

                    window.open(data.success, '_top');
                } else {
                    var pesan = "<div class='text-left alert alert-danger'>";
                    for (var i = 0; i < data.error.length; i++) {                                            
                        pesan += data.error[i]+'</br>'; 
                    }
                    pesan += '</div>';
                    
                    const wrapper = document.createElement('div');        
                    wrapper.innerHTML = pesan;
                    swal({
                        icon: "error",
                        content: wrapper                     
                    });
                } 
            },
            error: function (data) {
                console.log('Error:', data);
                $("#loader").hide();
            }
        });
    }
</script>
@endpush