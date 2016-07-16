(function($) {            
    
    //optimize multiple images
    $('.CMSFileOptimizeImageController #optimize-progress-bar').entwine({
        onmatch: function(e){
            var parentID = getParameterByName('ID');
            var progressBar = $(this).progressbar({});
            var optimizeInfo = $('#optimize-info');
            var progressCount = $('#progress-count');
            var count = 0;

            //get the IDs of the images to optimize
            $.ajax({
                url: $('base').attr('href') + "admin/assets/imagesToOptimize",
                data: {ParentID: parentID}, 
                success: function(result){
                    result = JSON.parse(result); 
                    
                    if(result.length){
                        progressBar.progressbar( "option", "max", result.length );

                        optimizeInfo.show();
                        progressCount.find('#max').html(result.length);

                        //optimize the images and update the progress bar each time
                        optimizeImage(result[0], result);
                    }else {                                                        
                        $('#no-images').show();
                    }
                },
                error: function(result){
                    //error
                }
            });

            /**
             * recursively optimize the images
             * @param {type} id
             * @param {type} result
             * @returns {undefined}
             */
            function optimizeImage(path, pathList){
                $.ajax({
                    url: $('base').attr('href') + "admin/assets/optimizeImage",
                    data: {image: path},
                    success: function(result){
                        result = JSON.parse(result);

                        //increase count
                        count = count + 1;

                        progressBar.progressbar( "value", count );

                        progressCount.find('#count').html(count);

                        $('#file-list').append('<li>Optimized '+result.Name+'. Optimized Size: '+result.OptimizedSize+'. Original Size: '+result.UnoptimizedSize+'</li>');
                                                
                        if(count < pathList.length){
                            optimizeImage(pathList[count], pathList);
                        }
                    }
                });
            }
        }            
    });

    //get query string
    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)", "i"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
              
})( jQuery );