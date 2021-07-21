
<!-- JAVASCRIPTS -->

<script src="{{Theme::asset()->url('plugins/jquery/jquery.js')}}"></script>
<script src="{{Theme::asset()->url('plugins/popper/popper.min.js')}}"></script>
<script src="{{Theme::asset()->url('plugins/slick/slick.min.js')}}"></script>
<script src="{{Theme::asset()->url('plugins/smoothscroll/SmoothScroll.min.js')}}"></script>
<script src="{{Theme::asset()->url('js/custom.js')}}"></script>
<script>
    $(document).ready(function(){
        
        $("#submit_checkpass").on('click',function(e){
            e.preventDefault();
            var pass = $("#pass_dow").val();
            
            $.ajax({
                url: "{{ route('ajax.checkpass') }}",
                type:'POST',
                dataType: "json",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "password": pass,
                },
                beforeSend: function() {
                    // setting a timeout
                    $("div#loading").css('display','block');
                },
                complete: function(){
                    $("div#loading").css('display','none');
                },
                success: function(data) {
                    if(data.status == 1) {

                        const link_android = "<?php echo env('LINKDOW_ANDROID')?>";
                        const link_ios = "<?php echo env('LINKDOW_IOS')?>";
                
                        const code = "<?php echo $code->code ?>";

                        $("div#input").css('display','none');
                        $("#list-inline").empty().append(
                            '<li class="list-inline-item"><a class="btn-update" href="'+link_android+'"><img class="img-fluid" src="{{Theme::asset()->url('images/app-badge/google-play.png')}}"></a></li>',
                            '<li class="list-inline-item"><a class="btn-update" href="'+link_ios+'/?code='+code+'"><img class="img-fluid" src="{{Theme::asset()->url('images/app-badge/app-store.png')}}"></a></li>' 
                        );

                    }else{
                        console.log(data.error);
                        $(".error_password").empty().append(
                            '<div class="alert mt-2 alert-danger">'+data.error.password+'</div>'
                        );
                    }
                },
            });
        });

        $('#list-inline').on('click', '.btn-update', function() {
          
            const id = "<?php echo $code->id?>";
            $.ajax({
                url: "{{ route('ajax.updatecode') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id,
                },
                success: function(response) {
                    if (response.status == 1) {
                        console.log(response);
                    } else {
                        console.log(response);
                    }
                    
                },
            });
        })
    });
    
</script>
</body>

</html>