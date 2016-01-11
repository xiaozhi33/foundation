// JavaScript Document
function secBoard(n) {
  var secTable=document.getElementById("secTable1");
  var mainTable=document.getElementById("mainTable1");
      //取对象最好用 document.getElementById('secTable');
      for(i=0;i<secTable.rows[0].cells.length;i++) //cells是td，rows是tr
        secTable.rows[0].cells[i].className="seca1";
      secTable.rows[0].cells[n].className="seca2";
      for(i=0;i<mainTable.tBodies.length;i++) //这里也一样，不过用FF试了试居然可以
        mainTable.tBodies[i].style.display="none";
      mainTable.tBodies[n].style.display="block";  
}