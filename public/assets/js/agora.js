// Handle errors.
let handleError = function(err){
        console.log("Error: ", err);
};

let appId = "ed0139b770d54fd885118259621ac8b3";
let token = $("#token").val();
let channelName = $("#channel_name").val();
let userid = $("#uid").val();

// Query the container to which the remote stream belong.
let remoteContainer = document.getElementById("remote-container");

// Add video streams to the container.
function addVideoStream(elementId){
        // Creates a new div for every stream
        let streamDiv = document.createElement("div");
        // Assigns the elementId to the div.
        streamDiv.id = elementId;
        // Takes care of the lateral inversion
        streamDiv.style.transform = "rotateY(180deg)";
        // Adds the div to the container.
        remoteContainer.appendChild(streamDiv);
};

// Remove the video stream from the container.
function removeVideoStream(elementId) {
        let remoteDiv = document.getElementById(elementId);
        if (remoteDiv) remoteDiv.parentNode.removeChild(remoteDiv);
};

function endCall(){
    client.leave();
    window.close();
}

let client = AgoraRTC.createClient({
    mode: "rtc",
    codec: "vp8",
});

client.init(appId, function() {
    console.log("client initialized");
}, function(err) {
    console.log("client init failed ", err);
});

client.join(token,
 channelName, userid, (uid)=>{
        let localStream = AgoraRTC.createStream({
            audio: true,
            video: false,
        });
        // Initialize the local stream
        localStream.init(()=>{
            // Play the local stream
            localStream.play("me");
            // Publish the local stream
            client.publish(localStream, handleError);
        }, handleError);
}, handleError);

// Subscribe to the remote stream when it is published
client.on("stream-added", function(evt){
    client.subscribe(evt.stream, handleError);
});

// Play the remote stream when it is subsribed
client.on("stream-subscribed", function(evt){
    let stream = evt.stream;
    let streamId = String(stream.getId());
    addVideoStream(streamId);
    console.log(streamId+" Joined");
    $('.top-right').notify({  
        message: { text: streamId+" Joined." }  
    }).show(); 
    stream.play(streamId);
});

// Remove the corresponding view when a remote user unpublishes.
client.on("stream-removed", function(evt){
    let stream = evt.stream;
    let streamId = String(stream.getId());
    stream.close();
    removeVideoStream(streamId);
});
// Remove the corresponding view when a remote user leaves the channel.
client.on("peer-leave", function(evt){
    let stream = evt.stream;
    let streamId = String(stream.getId());
    stream.close();
    removeVideoStream(streamId);
});


client.on("onTokenPrivilegeWillExpire", function(){
//After requesting a new token
client.renewToken(token);
});

client.on("onTokenPrivilegeDidExpire", function(){
//After requesting a new token
client.renewToken(token);
});
