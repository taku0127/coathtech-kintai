/******/ (() => { // webpackBootstrap
/*!********************************!*\
  !*** ./resources/js/script.js ***!
  \********************************/
// 現在日時表示

function updateTime() {
  var now = new Date();
  var formattedDate = now.getFullYear() + "年" + (now.getMonth() + 1) + "月" + now.getDate() + "日" + "(" + ["日", "月", "火", "水", "木", "金", "土"][now.getDay()] + ")";
  var formattedDateForValue = now.getFullYear() + "-" + (now.getMonth() + 1).toString().padStart(2, "0") + "-" + now.getDate().toString().padStart(2, "0");
  var formattedTime = now.getHours().toString().padStart(2, "0") + ":" + now.getMinutes().toString().padStart(2, "0");
  document.querySelectorAll(".js-currentDay").forEach(function (element) {
    if (element.tagName === "INPUT") {
      element.value = formattedDateForValue;
    } else {
      element.textContent = formattedDate;
    }
  });
  document.querySelectorAll(".js-currentTime").forEach(function (element) {
    if (element.tagName === "INPUT") {
      element.value = formattedTime; // input の場合は value を更新
    } else {
      element.textContent = formattedTime; // それ以外はテキストを更新
    }
  });
}

// 初回実行
updateTime();

// 60秒ごとに更新（次の00秒で更新）
setInterval(function () {
  var now = new Date();
  if (now.getSeconds() === 0) {
    updateTime();
  }
}, 1000);
/******/ })()
;