    <script type="application/x-javascript">

    dim = function(id, action, obj) {
      obj.parentNode.setAttribute('dimstate', action);
      postNumber(id, action);
    }
    
    postNumber = function(id, action) {
      req = new XMLHttpRequest();
      req.open("GET", '/post.php?id='+ id + '&action=' + action, true);
/*
        req.onreadystatechange = function () {
        if(req.readyState == 4)
          alert(req.responseText);     
      }
*/
      req.send();
    }

    refreshSnapshot = function() {
     element = document.getElementById('snapshot');
     element.src = "snapshot.php?" +  (new Date()).getTime();
     element.onload = refreshSnapshot;
    };
    window.setTimeout('refreshSnapshot', 1000);
    </script>
  </body>
</html>
