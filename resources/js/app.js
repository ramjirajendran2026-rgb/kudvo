import './bootstrap'
import './swal.js'

import AOS from 'aos'

window.AOS = AOS
document.addEventListener('DOMContentLoaded', () =>
    setTimeout(() => AOS.init({ duration: 800 }), 50),
)
