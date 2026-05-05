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
            <div id="smartwizard">

                <ul class="nav">
                    <li class="nav-item">
                      <a class="nav-link" href="#step-1">
                        <strong>Step 1</strong> <br>This is step description
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#step-2">
                        <strong>Step 2</strong> <br>This is step description
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="#step-3">
                        <strong>Step 3</strong> <br>This is step description
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link " href="#step-4">
                        <strong>Step 4</strong> <br>This is step description
                      </a>
                    </li>
                </ul>
        
                <div class="tab-content">
                    <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                        <h3>Step 1 Content</h3>
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                    </div>
                    <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
                        <h3>Step 2 Content</h3>
                        <div>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum. </div>
                    </div>
                    <div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3">
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                    </div>
                    <div id="step-4" class="tab-pane" role="tabpanel" aria-labelledby="step-4">
                        <h3>Step 4 Content</h3>
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                    </div>
                </div>
            </div>
        </div>
    </section> 
@endsection

@section('modal')
    
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toolbar extra buttons
        var btnFinish = $('<button></button>').text('Finish')
            .addClass('btn btn-info')
            .on('click', function() { 

            });

        var btnCancel = $('<button></button>').text('Cancel')
                                        .addClass('btn btn-danger')
                                        .on('click', function(){ $('#smartwizard').smartWizard("reset"); });

        $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection, stepPosition) {
            $("#prev-btn").removeClass('disabled');
            $("#next-btn").removeClass('disabled');
            
            if(stepPosition === 'first') {
                $("#prev-btn").addClass('disabled');
            } else if(stepPosition === 'last') {
                $("#next-btn").addClass('disabled');
            } else {
                $("#prev-btn").removeClass('disabled');
                $("#next-btn").removeClass('disabled');
            }
        });

        // Smart Wizard
        $('#smartwizard').smartWizard({
            selected: 0,
            theme: 'arrows', // default, arrows, dots, progress
            // darkMode: true,
            transition: {
                animation: 'slide-horizontal', // Effect on navigation, none/fade/slide-horizontal/slide-vertical/slide-swing
            },
            toolbarSettings: {
                toolbarPosition: 'both', // both bottom
                toolbarExtraButtons: [btnFinish, btnCancel]
            }
        });

        $("#prev-btn").on("click", function() {
            // Navigate previous
            $('#smartwizard').smartWizard("prev");
            return true;
        });

        $("#next-btn").on("click", function() {
            // Navigate next
            $('#smartwizard').smartWizard("next");
            return true;
        });
    });
</script>
@endpush