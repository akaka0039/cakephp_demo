$(document).ready(function () {
    /**
     * 送信ボタンクリック
     */
    $("#send").click(function () {
        var data = { request: $("#textdata").val() };
        alert($("#textdata").val());
        $(document).ajaxSend(function () {
            $("#overlay").fadeIn(300);
        });
        /**
         * Ajax通信メソッド
         * @param type  : HTTP通信の種類
         * @param url   : リクエスト送信先のURL
         * @param data  : サーバに送信する値
         */
        $.ajax({
            type: "POST",
            datatype: "json",
            url: "http://localhost:8765/ajax/add",
            data: data,
            success: function (data, dataType) {
                $("#overlay").fadeOut(300);
                alert("Success");
            },

            /**
             * Ajax通信が失敗した場合に呼び出されるメソッド
             */
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                alert("Error : " + errorThrown);
            },
        });
        return false;
    });
});
