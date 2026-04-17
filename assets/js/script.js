function sendToGmail() {
    const name = document.getElementById('clientName').value;
    const message = document.getElementById('clientMessage').value;
    const myEmail = "fekemark6@gmail.com";
    
    if (name === "" || message === "") {
        alert("Please fill in your name and message!");
        return;
    }

    const subject = encodeURIComponent("Project Inquiry from " + name);
    const body = encodeURIComponent("Name: " + name + "\n\nMessage:\n" + message);
    const gmailUrl = `https://mail.google.com/mail/?view=cm&fs=1&to=${myEmail}&su=${subject}&body=${body}`;
    
    window.open(gmailUrl, '_blank');
}

function openModal(id) {
    document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
    document.getElementById(id).style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

window.onclick = function(event) {
    if (event.target.className === 'modal-overlay') {
        event.target.style.display = 'none';
    }
}
