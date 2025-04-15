
// 現在日時表示

function updateTime(){
    const now = new Date();
    const formattedDate = now.getFullYear() + "年" + (now.getMonth() + 1) + "月" + now.getDate() + "日"+"("+["日", "月", "火", "水", "木", "金", "土"][now.getDay()]+")";

    const formattedDateForValue = now.getFullYear() + "-" +
    (now.getMonth() + 1).toString().padStart(2, "0") + "-" +
    now.getDate().toString().padStart(2, "0");

    const formattedTime = now.getHours().toString().padStart(2, "0") + ":" + now.getMinutes().toString().padStart(2, "0");

    document.querySelectorAll(".js-currentDay").forEach(element => {
        if (element.tagName === "INPUT") {
            element.value = formattedDateForValue;
        } else {
            element.textContent = formattedDate;
        }
    });

    document.querySelectorAll(".js-currentTime").forEach(element => {
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
setInterval(() => {
    const now = new Date();
    if (now.getSeconds() === 0) {
        updateTime();
    }
}, 1000);
