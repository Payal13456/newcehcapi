<!DOCTYPE html>
<html >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Chat App Socket.io | CodeCheef</title>
    </head>
    <body>

        <div class="container">
            <div class="row chat-row">
                <div class="chat-content">
                    <ul></ul>
                </div>
	    </div>
        </div>

       <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.4.1/socket.io.js" integrity="sha512-MgkNs0gNdrnOM7k+0L+wgiRc5aLgl74sJQKbIWegVIMvVGPc1+gc1L2oK9Wf/D9pq58eqIJAxOonYPVE5UwUFA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script>
	  var sock = io("http://cehcappapi.tryambaka.com:8081");
	  console.log(sock);
	  sock.on('video-call:App\\Events\\VideoCall', function (data){
	  	console.log("SOCKET DATA"+data.username);
	  });
	</script>
    </body>
</html>

