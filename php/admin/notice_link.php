<?php
session_start();
if (!isset($_SESSION["admin_user"])) {
  header("Location: index.php");
  exit;
}

include_once ("../include/config.php");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin - Notice Links</title>
  <link rel="stylesheet" href="css/main.css?v=<?=$ADMIN_VERSION?>">
  <style>
    table, th, td {
      border: 1px solid;
      border-collapse: collapse;
    }

    #tblNotice tr:hover {
      background-color: #ddd;
    }

    #tblNotice td {
      text-align: center;
    }

    #tblNotice td.left {
      text-align: left;
    }

    #tblNotice td.top {
      vertical-align: top;
    }

    #tblNotice input {
      width: 320px;
    }

    .form-row {
      display: flex;
      justify-content: start;
    }

    .form-row > div {
      padding-right: 20px;
    }

    .form-row input {
      width: 380px;
    }
  </style>
</head>
<body>
  <div class="header bgYellow">
    <div><h2><a href="index.php">« Admin Index</a></h2></div>
    <div class="title">Notice Links</div>
    <div><?=$_SESSION["admin_user"]?> | <a href="logout.php">Logout</a></div>
  </div>
  <div class="body">
  <div style="margin:10px 0;text-decoration:underline">Create</div>
    <div class="form-row">
      <div>
        <div>Notice (EN)</div>
        <div>
          <input id="tbTitleEn_new" placeholder="Title" />
          <br />
          <input id="tbUrlEn_new" placeholder="URL" />
        </div>
      </div>
      <div>
        <div>Notice (TC)</div>
        <div>
          <input id="tbTitleTc_new" placeholder="標題" />
          <br />
          <input id="tbUrlTc_new" placeholder="URL" />
        </div>
      </div>
      <div>
        <div>Notice (SC)</div>
        <div>
          <input id="tbTitleSc_new" placeholder="标题" />
          <br />
          <input id="tbUrlSc_new" placeholder="URL" />
        </div>
      </div>
      <div>
        <div>Seq <input id="tbSeq_new" type="text" style="width: 25px" value="0" maxlength="3"></div>
        <br />
        <Select id="ddlStatus_new">
          <option value="A">Active</option>
          <option value="I" selected>Inactive</option>
        </Select>      
      </div>
    </div>
    <div style="margin-top:15px">
      <button id="btnCreate" style="margin-right: 20px" data-i="new" data-id="0" onclick="UpdateNoticeLink(this)">Create</button>
      <button onclick="ResetNotice()">Reset</button>
    </div>
    <hr style="width: 1260px; margin-left: 0" />
    <table>
      <thead>
        <tr>
          <th style="width: 50px">ID</th>
          <th style="width: 330px">EN</th>
          <th style="width: 330px">TC</th>
          <th style="width: 330px">SC</th>
          <th style="width: 50px">Seq</th>
          <th style="width: 80px">Status</th>
          <th style="width: 60px"></th>
        </tr>
      </thead>
      <tbody id="tblNotice"></tbody>
    </table>
    <button style="position: fixed; right: 20px; bottom: 60px; font-size: 20px" onclick="document.body.scrollTop=document.documentElement.scrollTop=0">🔝</button>
    <button style="position: fixed; right: 20px; bottom: 20px; width:36px; height: 36px; font-size: 20px" onclick="window.scrollTo(0, document.body.scrollHeight)">⟱</button>
    <script>
      function GetDdl(id, selectedValue) {
        var select = document.createElement("select");
        var option;
        var values = {
          "A": "Active",
          "I": "Inactive",
        }

        select.setAttribute("id", id);
        Object.keys(values).forEach(function(k) {
          var option = document.createElement("option");
          option.value = k;
          option.textContent = values[k];
          if (selectedValue == k) {
            option.setAttribute("selected", "selected");
          }
          select.appendChild(option);
        });

        return select;
      }

      function ResetNotice() {
        document.getElementById("tbTitleEn_new").value = "";
        document.getElementById("tbUrlEn_new").value = ""; 
        document.getElementById("tbTitleTc_new").value = "";
        document.getElementById("tbUrlTc_new").value = ""; 
        document.getElementById("tbTitleSc_new").value = "";
        document.getElementById("tbUrlSc_new").value = ""; 
        document.getElementById("tbSeq_new").value = "0"; 
        document.getElementById("ddlStatus_new").value = "I";
        document.getElementById("btnCreate").removeAttribute("disabled");
      }

      function UpdateNoticeLink(btn) {
        var i = btn.getAttribute("data-i");
        var noticeId = parseInt(btn.getAttribute("data-id"), 10);

        if (noticeId == 0) {
          document.getElementById("btnCreate").setAttribute("disabled", "disabled");
        }

        var postData = {
          notice_id: noticeId,
          title_en: document.getElementById("tbTitleEn_" + i).value.trim(),
          url_en: document.getElementById("tbUrlEn_" + i).value.trim(),
          title_tc: document.getElementById("tbTitleTc_" + i).value.trim(),
          url_tc: document.getElementById("tbUrlTc_" + i).value.trim(),
          title_sc: document.getElementById("tbTitleSc_" + i).value.trim(),
          url_sc: document.getElementById("tbUrlSc_" + i).value.trim(),
          seq: parseInt(document.getElementById("tbSeq_" + i).value, 10),
          status: document.getElementById("ddlStatus_" + i).value,
        };

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../en/api/admin-updateNoticeLink");
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.onreadystatechange = function () {
          if (this.readyState == 4) {
            if (this.status == 200) {
              const jsonData = JSON.parse(this.responseText);

              if (jsonData.status == "success") {
                if (noticeId > 0) {
                  var tdNoticeId = document.getElementById("tdNoticeId_" + i);
                  tdNoticeId.classList.remove("highlight-text");
                  setTimeout(() => {
                    tdNoticeId.classList.add("highlight-text");
                  }, 100);
                } else {
                  ResetNotice();
                  GetData();
                }
              } else {
                alert("Update failed: " + jsonData.error);  
              }
            } else {
              alert("Error: " + this.responseText);
            }
          }
        };

        xhr.send(JSON.stringify(postData));
      }

      function GetData(append, start, pageSize) {
        var apiUrl = "../en/api/admin-listNoticeLink";

        var xhr = new XMLHttpRequest();
        
        xhr.open("GET", apiUrl);
        xhr.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
            var noticeList = JSON.parse(this.responseText);

            if (Array.isArray(noticeList)) {
              var tblNotice = document.getElementById("tblNotice");
              tblNotice.innerHTML = "";

              for (var i = 0; i < noticeList.length; ++i) {
                const curNotice = noticeList[i];
                
                var row = tblNotice.insertRow();
                var td0 = row.insertCell(0);
                td0.appendChild(document.createTextNode(curNotice.id));
                td0.setAttribute("id", "tdNoticeId_" + i);

                // EN
                var tbTitleEn = document.createElement("input");
                tbTitleEn.setAttribute("id", "tbTitleEn_" + i);
                tbTitleEn.setAttribute("value", curNotice.title_en);
                var tbUrlEn = document.createElement("input");
                tbUrlEn.setAttribute("id", "tbUrlEn_" + i);
                tbUrlEn.setAttribute("value", curNotice.url_en);

                var td1 = row.insertCell(1);
                td1.appendChild(tbTitleEn);
                td1.appendChild(document.createElement("br"));                  
                td1.appendChild(tbUrlEn);

                // TC
                var tbTitleTc = document.createElement("input");
                tbTitleTc.setAttribute("id", "tbTitleTc_" + i);
                tbTitleTc.setAttribute("value", curNotice.title_tc);
                var tbUrlTc = document.createElement("input");
                tbUrlTc.setAttribute("id", "tbUrlTc_" + i);
                tbUrlTc.setAttribute("value", curNotice.url_tc);

                var td2 = row.insertCell(2);
                td2.appendChild(tbTitleTc);
                td2.appendChild(document.createElement("br"));                  
                td2.appendChild(tbUrlTc);

                // SC
                var tbTitleSc = document.createElement("input");
                tbTitleSc.setAttribute("id", "tbTitleSc_" + i);
                tbTitleSc.setAttribute("value", curNotice.title_sc);
                var tbUrlSc = document.createElement("input");
                tbUrlSc.setAttribute("id", "tbUrlSc_" + i);
                tbUrlSc.setAttribute("value", curNotice.url_sc);

                var td3 = row.insertCell(3);
                td3.appendChild(tbTitleSc);
                td3.appendChild(document.createElement("br"));                  
                td3.appendChild(tbUrlSc);

                // Seq
                var tbSeq = document.createElement("input");
                tbSeq.setAttribute("id", "tbSeq_" + i);
                tbSeq.setAttribute("value", curNotice.seq);
                tbSeq.setAttribute("style", "width: 30px");
                tbSeq.setAttribute("maxlength", "3");

                var td4 = row.insertCell(4);
                td4.appendChild(tbSeq);

                // Status
                row.insertCell(5).appendChild(GetDdl("ddlStatus_" + i, curNotice.status));                

                var btnUpdate = document.createElement("button");
                btnUpdate.appendChild(document.createTextNode("Update"));
                btnUpdate.setAttribute("data-i", i);
                btnUpdate.setAttribute("data-id", curNotice.id);
                btnUpdate.onclick = function () {
                  UpdateNoticeLink(this);
                }

                row.insertCell(6).appendChild(btnUpdate);
              }
            }
          }
        }
        
        xhr.send();
      }

      GetData();
    </script>
  </div>
</body>
</html>