import Swal from 'sweetalert2';

window.Swal = Swal;

window.tts = function (text) {
    // Check if the browser supports speech synthesis
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(text);

        // Set properties (optional)
        utterance.pitch = 1; // 0 to 2
        utterance.rate = 1; // 0.1 to 10
        utterance.volume = 1; // 0 to 1
        utterance.lang = 'en-IN'; // Language code

        // Speak the text
        window.speechSynthesis.speak(utterance);
    } else {
        console.log('speechSynthesis not available');
    }
};
