function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    //ev.dataTransfer.setData("text", ev.target.id);
    console.log("Dragging:: ", ev.target.id);
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
    console.log("Dropping:: ", data);
    ev.target.appendChild(document.getElementById(data));
}
