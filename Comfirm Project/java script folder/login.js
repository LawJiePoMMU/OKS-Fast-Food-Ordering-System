function togglePass(id) {
    var input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}