<div id="">
    <div class="panel_s no-shadow" data-page="0" id="deal_activities_wrapper_scroller" style="max-height: 49vh;overflow-y: auto;">
        <div class="clearfix"></div>
    </div>
</div>

<script>
       function load_deal_activity_log(page){
         $.ajax({
            type: 'GET',
            url: admin_url+'projects/load_more_activities/'+<?php echo $project->id ?>,
            data: {page:page},
            dataType: "json",
            success: function(resultData) { 
               if(resultData.success==true){
                  if(resultData.content){
                     console.log(page);
                     if(page ==0){
                        $('#deal_activities_wrapper_scroller').html(resultData.content);
                     }else{
                        $('#deal_activities_wrapper').append(resultData.content);
                     }
                    
                     $('#deal_activities_wrapper_scroller').attr('data-page',parseInt(page)+1);
                  }else{
                     if(page ==0){
                        $('#deal_activities_wrapper_scroller').html('No records found');
                     }
                     hasMoreLogs =false;
                  }
               }else{
                  hasMoreLogs =false;
               }
            }
         });
      }
   function init_deal_activities_log(){
      $('#deal_activities_wrapper_scroller').attr('data-page',0);
      load_deal_activity_log(0);
   }
   document.addEventListener("DOMContentLoaded", function(event) { 

    init_deal_activities_log();
    $('#deal_activities_wrapper_scroller').on('scroll', function() {
            
            if(hasMoreLogs ==true){
                if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                var page =$(this).attr('data-page');
                load_deal_activity_log(page);
                }
            }
        })
    });
</script>