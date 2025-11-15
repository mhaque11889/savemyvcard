<html>
  <head>
<style>
img{
  display: block;
  padding: 2px;
  background-color: #fff
}
</style>
  </head>
  <body style="margin: 0px; height: 100%; background-color: rgba(0,0,0,1)">

  <div id="qrcode"></div>

  <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
  <script>
    const qrcode = new QRCode(document.getElementById('qrcode'), {
        text: 'My Name is Manzar',
        width: 200,
        height: 200,
        colorDark : '#000',
        colorLight : '#fff',
        correctLevel : QRCode.CorrectLevel.H
    });
  </script>
  </body>
</html>
