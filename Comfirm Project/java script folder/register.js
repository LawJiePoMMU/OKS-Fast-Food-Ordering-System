function togglePass(id) {
    var input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}
const today = new Date().toISOString().split('T')[0];
document.getElementById("birthdayInput").setAttribute("max", today);