<!DOCTYPE html>
<html>
    <head>
        <title>Compare</title>

        <link rel="stylesheet" type="text/css" href="vendor/bower_components/bootstrap/dist/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="css/mystyle.css">
        
        <script src="vendor/bower_components/jquery/dist/jquery.js"></script>
        <script src="vendor/bower_components/bootstrap/dist/js/bootstrap.js"></script>
        <script src="vendor/bower_components/fileapi/dist/FileAPI.js"></script>
        <script src="js/myscript.js"></script>
        
    </head>
    <body>
        <style>
            .compare{
                display: table;
            }
            
            .l-touched{
                background-color:rgba(202,202,60,0.5);
            }
            
            .c-changed{
                background-color:#EFBFBF;
            }
            
            .c-deleted{
                background-color:#E83535;
            }
            
            .c-added{
                background-color:#46AF06;
            }
            
            .compare-line{
                display:table-row;
                height:13px;
            }
            
            .counter{
                display:table-cell;
            }
            
        </style>
        <div class="container">
            <div class="content">
                <div class="title">Laravel 5</div>
            </div>
            
            <div>
        <!-- "js-fileapi-wrapper" -- required class -->
        <div class="js-fileapi-wrapper upload-btn">
            <div class="upload-btn__txt">Choose files</div>
            <input id="choose" name="files" type="file" multiple />
        </div>
                
                <div style="overflow:hidden;" id="compare" class="col-md-12">
                
                </div>
                
                
        
        </div>
            <div class="c-changed">
                    Changed word
                </div>
                <div class="c-deleted">
                    Deleted word
                </div>
                <div class="c-added">
                    Added word
                </div>
                <div class="l-touched">
                    Touched line
                </div>
    </body>
</html>
