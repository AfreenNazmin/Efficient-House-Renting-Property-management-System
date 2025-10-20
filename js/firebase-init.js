
  // Import the functions you need from the SDKs
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.1.0/firebase-app.js";
  import { getAuth, RecaptchaVerifier, signInWithPhoneNumber, sendEmailVerification, createUserWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/10.1.0/firebase-auth.js";

  // Your Firebase config
  const firebaseConfig = {
    apiKey: "AIzaSyB3iim_58j3U8eesAvhTG23XDMx__CQ694",
    authDomain: "efficient-house-renting.firebaseapp.com",
    projectId: "efficient-house-renting",
    storageBucket: "efficient-house-renting.firebasestorage.app",
    messagingSenderId: "140800704227",
    appId: "1:140800704227:web:77b39d71e5c2a879640fd6",
    measurementId: "G-YGQFW2RKQF"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const auth = getAuth(app);

  // You can now use `auth` for phone/email OTP verification

  import { RecaptchaVerifier, signInWithPhoneNumber } from "https://www.gstatic.com/firebasejs/10.1.0/firebase-auth.js";

const phoneInput = document.getElementById("phone");
const countryCode = document.getElementById("countryCode");

const fullPhoneNumber = countryCode.value + phoneInput.value;

// Setup reCAPTCHA
window.recaptchaVerifier = new RecaptchaVerifier(auth, 'recaptcha-container', {
  size: 'invisible',
  callback: (response) => {
    // reCAPTCHA solved — proceed
    sendOTP();
  }
});

function sendOTP() {
  signInWithPhoneNumber(auth, fullPhoneNumber, window.recaptchaVerifier)
    .then((confirmationResult) => {
      window.confirmationResult = confirmationResult;
      alert("OTP sent! Please check your phone.");
    })
    .catch((error) => {
      console.error(error);
      alert("Failed to send OTP: " + error.message);
    });
    document.getElementById("otpSection").style.display = "block";

}
const otpCode = document.getElementById("otpInput").value;

window.confirmationResult.confirm(otpCode)
  .then((result) => {
    const user = result.user;
    alert("✅ Phone number verified!");
    // Proceed with signup or redirect
  })
  .catch((error) => {
    alert("❌ Invalid OTP: " + error.message);
  });
