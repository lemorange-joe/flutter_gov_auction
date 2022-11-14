var scrollTimeout;

function AutoScroll(d) {
  window.scrollBy({top: d});
  scrollTimeout = setTimeout(function() {
    AutoScroll(d);
  }, 20);
}

function JumpScroll(d) {
  window.scrollBy({top: d});
}

function StopScroll() {
  clearTimeout(scrollTimeout);
}

function TempDisableButton(id) {
  document.getElementById(id).classList.add("disabled");
  document.getElementById(id).setAttribute("disabled", "disabled");

  setTimeout(function() {
    document.getElementById(id).removeAttribute("disabled");
    document.getElementById(id).classList.remove("disabled");
  }, 5000);
}
