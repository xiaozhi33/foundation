// JavaScript Document
function secBoard(n) {
  var secTable=document.getElementById("secTable1");
  var mainTable=document.getElementById("mainTable1");
      //ȡ��������� document.getElementById('secTable');
      for(i=0;i<secTable.rows[0].cells.length;i++) //cells��td��rows��tr
        secTable.rows[0].cells[i].className="seca1";
      secTable.rows[0].cells[n].className="seca2";
      for(i=0;i<mainTable.tBodies.length;i++) //����Ҳһ����������FF�����Ծ�Ȼ����
        mainTable.tBodies[i].style.display="none";
      mainTable.tBodies[n].style.display="block";  
}