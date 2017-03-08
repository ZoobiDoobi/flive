//My Apps JavaScript
$(document).ready(function(){
    
    var endPointUrl = 'https://livotes.com/';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    $('#facebookPagesForm').on('submit' , function(event){
        event.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url : endPointUrl + 'campaign/saveFacebookPage',
            method : 'POST',
            dataType : 'json',
            data : formData,
            beforeSend : function(){
                $('#loading').css('display' , 'block');
                $('#facebookPagesForm').css('display' , 'none');
            }
        }).done(function(data , textStatus , jqXHR){
            if(! data.success && !data.subscription){
                $('#loading').css('display' , 'none');
                $('#facebookPagesForm').css('display' , 'block');
                
                if(data.errors.fbPage){
                    alertify.error(data.errors.fbPage);
                }
                else if(data.errors.subscriptionError){
                    alertify.error(data.errors.subscriptionError);
                }
            }
            else{
                alertify.success('Your Page Has Successfully Subscribed To Our App!');
                appendLiveVideos();
                $('#facebookPagesPanel').css('display' , 'none');
                $('#createCampaignPanel').css('display', 'block');
            }
        }).fail(function(data , textStatus , errorThrown){
            alertify.error('We are having trouble connecting to service!');
            $('#facebookPagesForm').css('display' , 'block');
        }).always(function(data , textStatus , jqXHR){
            $('#loading').css('display' , 'none');
        });

    });
    
    function getLiveVideos(fn){
        
        $.ajax({
            url : endPointUrl + 'liveVideo/get',
            method : 'GET',
            dataType : 'json',
        }).done(function(data , textStatus , jqXHR){
            if(data.success){
                fn(data);
            }
            else{
                if(data.errors.noLiveVideos){
                    alertify.error(data.errors.noLiveVideos);
                }
            }
        }).fail(function(data , textStatus , jqXHR){
            alertify.error('We are having trouble connecting to service!');
            console.log(textStatus);
        });
    }
    
    function appendLiveVideos(){
        
        getLiveVideos(function(data){
            $('#videos').empty();
            $('#videos').append('<option value="">Select Live Video</option>');
            $.each(data.liveVideos , function(index , item){
               $('#videos').append(
                    $('<option></option>').val(item.id).html(item.title).attr('data-status' , item.status)
                ); 
            });
        });

    }
    
    $('#createCampaignBtn').on('click' , function(event){
        var formElement = document.getElementById("createCampaignForm");
        
        $.ajax({
            url : endPointUrl +'campaign/store',
            method : 'POST',
            headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data : new FormData(formElement),
            contentType : false,
            processData : false,
            dataType : 'json',
            beforeSend : function(){
                $('#campaignloading').css('display' , 'block');
                $('#createCampaignForm').css('display' , 'none');
            }
        }).done(function(data, textStatus, jqXHR){
 
           if(! data.success){
               if(data.errors.campaignName){
                   alertify.error(data.errors.campaignName);
               }
               if(data.errors.keywords){
                   alertify.error(data.errors.keywords);
               }
               if(data.errors.campaignSave){
                   alertify.error(data.errors.campaignSave);
               }
           }
           else{
               
               alertify.success('Campaign Successfully Saved!');
               $('#campaignloading').css('display' , 'none');
               $('#createCampaignPanel').css('display' , 'none');
               $('#urlTextBox').val(data.url);
               $('#campaignUrl').css('display' , 'block');    
           }
        }).fail(function(data, textStatus, jqXHR){
            alertify.error('We are having trouble connecting to service');
            $('#campaignloading').css('display' , 'none');
            $('#createCampaignForm').css('display' , 'block');
        }).always(function(data){
            $('#campaignloading').css('display' , 'none');
        });
    });


