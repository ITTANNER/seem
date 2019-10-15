const socket = io.connect('http://localhost:3000');
//DOM elements
let start = document.getElementById('start');
let stop = document.getElementById('stop');
let status = document.getElementById('status');

start.addEventListener('click', function(){
    socket.emit('chat:motor',{
       motor:start.value,
   });    
});
stop.addEventListener('click', function(){
    socket.emit('chat:motor',{
       motor:stop.value,
   });    
});
socket.on('chat:motor', function(data){    
    status.innerHTML = `<strong>${data.motor}</strong>`;    
});