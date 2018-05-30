<!doctype HTML>
<html>
  <head>
      <title>SicappContainer</title>
      <link href="css/bootstrap.css" rel="stylesheet" media="screen">
      <script src="js/jquery.min.js" type="text/javascript"></script>
      <style>
        body, html {
          margin: 0;
          padding: 0;
        }
        #box {
          width: 100%;
          height: 100%;
          position: absolute;
        }
        #box_left, #box_right {
          float: left;
          width: 44%;
          min-height: 90%;
        }
        #box_mid {
          float: left;
          width: 10%;
          min-height: 90%;
        }
        #box_mid div {
          margin-top: 40vh;
          margin-left: 30%;
        }
        textarea {
          height: 89vh;
          width: 44vw;
          overflow-y: scroll;
          resize: none;
          background: lightgrey;
        }
        #header {
          height: 10vh;
          width: 100vw;
          font-size: 18pt;
          font-weight: bold;
        }
      </style>
  </head>
  <body>
      <div id="box">
        <div id="header"><center>SicappContainer</center></div>
        <div id="box_left">
          <textarea></textarea>
        </div>
        <div id="box_mid">
          <div>
            HTTPS <input type="checkbox" id="radio"><hr/>
            <button class="btn"><i class="glyphicon glyphicon-arrow-right"></i></button>
          </div>
        </div>
        <div id="box_right">
          <textarea></textarea>
        </div>
      </div>
      <script>
      $("#box_mid .btn").click(function() {
        var proto = "http";
        if ($("#radio").is(':checked')) {
          proto = "https";
        }
        data = {"request": $("#box_left textarea").val(),"proto":proto};
        $.ajax({
            url: "logic.php",
            type: 'POST',
            data: data,
            cache: false,
            error: function(){
                alert("error");
                return "";
            },
            success: function(data){
              $("#box_right textarea").val(data);
            }
      });
    });

    </script>
  </body>
</html>
