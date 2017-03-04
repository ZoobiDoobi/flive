//My Apps JavaScript
$(document).ready(function(){
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $('#campaignForm').on('submit' , function(event){
        
       event.preventDefault();
       var formData = $(this).serialize();
       
       $.ajax({
           type : 'POST',
           url  : 'home/campaign',
           data : formData,
           dataType : 'json',
           beforeSend : function(){
               $('#loading').css('display','block');
               $('#campaignForm').css('display' , 'none');
           }
       }).done(function(data){
           $('#loading').css('display','none');
           console.log(data);
           
           if(! data.success){
               $('#campaignForm').css('display' , 'block');
               if(data.errors.campaignName){
                   alertify.error(data.errors.campaignName);
               }
               
           }
           else{
               $('.first-div').css('display', 'none');
               $(".pages-div").toggle('slide' , {direction : 'right'} , 500);
           }
       });
       
    });
    
});


