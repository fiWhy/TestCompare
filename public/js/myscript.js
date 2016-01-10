var blocksObj = {
        
        blocksLength:0,
        
        currentLength:0,
        
        buildCompareBlocks: function(blocks){
            var self = this;
            var compareBlock = $('#compare');
            for(var i = 0; i < blocks.length; i++){
                compareBlock.append('<div id="compare-bl-1" class="col-md-6 col-xs-6 compare"></div>');
                
                var currentBlock = $(compareBlock.children('div')[i]);
                
                for(var inn = 0; inn < blocks[i].length; inn++){
                    var cursor = blocks[i][inn][0];
                    currentBlock.append('<div class="compare-line">' +
                                        '<div class="counter col-xs-1">' + (blocks[i][inn][1]?(inn+1):'') + '</div>' + 
                                        '<div class="inn col-xs-9">' + cursor + '</div>' +
                                        '</div>');
                }
                
            }
        },
        
        loadFiles: function(e){
            var self  = this; 
            var files = FileAPI.getFiles(e.target);
            FileAPI.filterFiles(files, function (file, info){
                if( /^text/.test(file.type) ){
                    return info;
                }
                alert('Wrong type!');
                return  false;
            }, self.filesLoaded);
        },
        
        uploadProcess: function(file, path, process, complete){
            var self = this;
            FileAPI.upload({
                 url: path+ '?count=' + self.blocksLength,
                    files: { 
                        images: file
                    },
                    progress: function (evt){  
                        var pr = evt.loaded/evt.total * 100;
                        process(pr);
                    },
                    complete: function (err, xhr){
                        complete(err?false:xhr);
                        }
                  
                });
        },
        
        filesLoaded: function(files, rejected){
                blocksObj.blocksLength = files.length;
                if( files.length ){
                     blocksObj.uploadProcess(files, '/compare/savefiles', function(process){
                            console.log(process);
                        }, function(xhr){
                         console.log(xhr);
                         var json = JSON.parse(xhr.response);
                            blocksObj.buildCompareBlocks(json.body);
                        });
                }
        } 
    };

$(document).ready(function(){
    $('#choose').on('change', function(e){
      blocksObj.loadFiles(e);  
    })
}) 