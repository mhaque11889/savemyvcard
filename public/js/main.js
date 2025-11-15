/**
* Template Name: NiceAdmin - v2.2.2
* Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/
var persontype = '';
var personName = [];


(function() {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    if (all) {
      select(el, all).forEach(e => e.addEventListener(type, listener))
    } else {
      select(el, all).addEventListener(type, listener)
    }
  }

  /**
   * Easy on scroll event listener 
   */
  const onscroll = (el, listener) => {
    el.addEventListener('scroll', listener)
  }

  /**
   * Sidebar toggle
   */
  if (select('.toggle-sidebar-btn')) {
    on('click', '.toggle-sidebar-btn', function(e) {
      select('body').classList.toggle('toggle-sidebar');
      let sidebar = $('#sidebar').css('left');
      if(sidebar == '0px'){
        $('#sidebar').css('left','-300px');
        $('#main').css('margin-left','0px');
      }else{
        $('#sidebar').css('left','0px');
        $('#main').css('margin-left','300px');
      }
    })
  }

  /**
   * Search bar toggle
   */
  if (select('.search-bar-toggle')) {
    on('click', '.search-bar-toggle', function(e) {
      select('.search-bar').classList.toggle('search-bar-show')
    })
  }

  /**
   * Navbar links active state on scroll
   */
  let navbarlinks = select('#navbar .scrollto', true)
  const navbarlinksActive = () => {
    let position = window.scrollY + 200
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return
      let section = select(navbarlink.hash)
      if (!section) return
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        navbarlink.classList.add('active')
      } else {
        navbarlink.classList.remove('active')
      }
    })
  }
  window.addEventListener('load', navbarlinksActive)
  onscroll(document, navbarlinksActive)

  /**
   * Toggle .header-scrolled class to #header when page is scrolled
   */
  let selectHeader = select('#header')
  if (selectHeader) {
    const headerScrolled = () => {
      if (window.scrollY > 100) {
        selectHeader.classList.add('header-scrolled')
      } else {
        selectHeader.classList.remove('header-scrolled')
      }
    }
    window.addEventListener('load', headerScrolled)
    onscroll(document, headerScrolled)
  }

  /**
   * Back to top button
   */
  let backtotop = select('.back-to-top')
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add('active')
      } else {
        backtotop.classList.remove('active')
      }
    }
    window.addEventListener('load', toggleBacktotop)
    onscroll(document, toggleBacktotop)
  }

  /**
   * Initiate tooltips
   */
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })

  /**
   * Initiate quill editors
   */
  if (select('.quill-editor-default')) {
    new Quill('.quill-editor-default', {
      theme: 'snow'
    });
  }

  if (select('.quill-editor-bubble')) {
    new Quill('.quill-editor-bubble', {
      theme: 'bubble'
    });
  }

  if (select('.quill-editor-full')) {
    new Quill(".quill-editor-full", {
      modules: {
        toolbar: [
          [{
            font: []
          }, {
            size: []
          }],
          ["bold", "italic", "underline", "strike"],
          [{
              color: []
            },
            {
              background: []
            }
          ],
          [{
              script: "super"
            },
            {
              script: "sub"
            }
          ],
          [{
              list: "ordered"
            },
            {
              list: "bullet"
            },
            {
              indent: "-1"
            },
            {
              indent: "+1"
            }
          ],
          ["direction", {
            align: []
          }],
          ["link", "image", "video"],
          ["clean"]
        ]
      },
      theme: "snow"
    });
  }

  

  /**
   * Initiate Bootstrap validation check
   */
  var needsValidation = document.querySelectorAll('.needs-validation')

  Array.prototype.slice.call(needsValidation)
    .forEach(function(form) {
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })

  /**
   * Initiate Datatables
   */
  const datatables = select('.datatable', true)
  datatables.forEach(datatable => {
    new simpleDatatables.DataTable(datatable);
  })

  /**
   * Autoresize echart charts
   */
  const mainContainer = select('#main');
  if (mainContainer) {
    setTimeout(() => {
      new ResizeObserver(function() {
        select('.echart', true).forEach(getEchart => {
          echarts.getInstanceByDom(getEchart).resize();
        })
      }).observe(mainContainer);
    }, 200);
  }

})();

/***Custom Function Started */
$('#changePasswordButton').click(function() {
  // Get the values of the password fields
  var newPassword = $('#newPassword').val();
  var confirmPassword = $('#confirmPassword').val();

  // Compare the values
  if (newPassword === confirmPassword) {
    document.getElementById("changePasswordForm").submit();
  } else {
      $('#errorMessage').html("New Password and Confirm Password does not match. Please try again")
  }
});

$("#memberRegistration").submit(function(e){
  let memberType = $('#memberType').val();
  let email = $('#email').val();
  if(memberType == 'aes-staff')
  {
    if(!email.includes('aes.ac.in'))
    {
      alert("You have to provide your office email if you have selected AES-Staff");
      e.preventDefault();
    }
    else{
      console.log("Okay");
      document.getElementById("submitButton").disabled = true;
      document.getElementById("submitButton").innerHTML = "Please wait. Saving Data";
      // Submit the form
      document.getElementById("myForm").submit();
    }
  }
  
});

$()

function getSelectedCourse()
{
  const selectedCheckboxes = document.querySelectorAll('input[type="checkbox"]:checked');
  const selectedValues = [];
  selectedCheckboxes.forEach((checkbox) => {
    selectedValues.push(checkbox.value);
  });
  console.log(selectedValues);
  const userid = document.getElementById('userid').value;

  if(selectedValues.length == 0)
  {
    alert("Please select atleast one course.");
    return false;
  }

  let rep = confirm('Are you sure?');
  if(rep){
    const CSRF_TOKEN = $('[name="_token"]').val();
    $.ajax({
      type:'POST',
      url:'saveSelectedCourse',
      data: {_token: CSRF_TOKEN, message: {'selectedCourse': selectedValues, 'userid' : userid}},
      success:function(response) {
        console.log(response);
        if(response[0] == "success"){
          window.location.href = 'payNow';
        }
      }
    });
  }
}

function addRemovePayment(a, b)
{
  let amount = document.getElementById(b).value;
  let totalAmount = $('#totalAmountBox').val();
  var button = document.getElementById("payButton");

  if(a)
  {
    let sum = parseFloat(totalAmount)+parseFloat(amount);
    $('#totalAmountBox').val(sum);
    button.removeAttribute("disabled");
  }
  else
  {
    let sum = parseFloat(totalAmount)-parseFloat(amount);
    $('#totalAmountBox').val(sum);
    if(sum > 0)
    {
      button.removeAttribute("disabled");
    }
    else{ 
      button.disabled = true;
    }
  }
}

function enablePayNowButton()
{
  var checkboxes = $('[data-week="confirmationTickBox"]');
  let len = checkboxes.length;
  let count=0;
  checkboxes.each(function() {
    if($(this).prop('checked'))
    {
      count++;
    }
  });
  if(count >= len)
  {
    return true;
  }
  else
  {
    return false;
  }
} 

function transferToPayment()
{
  let cb1 = enablePayNowButton();
  if(cb1)
  {
    let checkboxes = document.querySelectorAll('input[type="checkbox"][data-student="list"]');
    let username = $('#username').val();
    let userid = $('#userid').val();
    let total = $('#totalAmountBox').val();
    let studentIds = [];
  
    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
          studentIds.push(checkbox.getAttribute('data-courseid'));
        }
    });
  
    let queryString = `activityName=Lifelong_Learning&uniqueId=${studentIds.join(",")}&Email=${username}&Amount=${total}&Remarks`;
    let encodedQueryString = btoa(queryString);
  
    let url = 'https://bo.aes.ac.in/AesPay/Eventpage.aspx?data='+rot13(encodedQueryString);
  
    const CSRF_TOKEN = $('[name="_token"]').val();  
  
    //console.log(studentIds); RemarksUniqueId
    $.ajax({
      type:'POST',
      url:'saveSettings',
      data: {_token: CSRF_TOKEN, message: {'action' : 'updatePaymentStatusWaiting', 'courseIds' : studentIds, 'userid' : userid}},
      success:function(data) {
        
      }
    });
  
    $('#paymentPageTermsAndCondition').html('Please wait for few minutes while we confirm the payment from Business office. This process might take upto 15 minutes. Thank you.');
    window.open(url);
  }
  else{
    alert("Please select all Terms and Conditions");
  }  
}

function rot13(input) {
  let output = '';
  for (let i = 0; i < input.length; i++) {
      let charCode = input.charCodeAt(i);
      if (charCode >= 65 && charCode <= 90) {  // Uppercase letters (A-Z)
          output += String.fromCharCode(((charCode - 65 + 13) % 26) + 65);
      } else if (charCode >= 97 && charCode <= 122) {  // Lowercase letters (a-z)
          output += String.fromCharCode(((charCode - 97 + 13) % 26) + 97);
      } else {
          output += input[i];  // Keep non-alphabetic characters unchanged
      }
  }
  return output;
}

function changeFirstMessageValue(value)
{
  let name = $('#vcardName').val();
  if(value == 1)
  {
    $('#firstMessage').val('Hi SaveMyVCard, Please share the contact card of '+name);
  }
  else if(value == 2)
  {
    $('#firstMessage').val('Hi '+name+', I got your contact by scanning Save my vcard QR Code. Can we connect?');
  }
  else{
    $('#firstMessage').val('Hi');
  }
}

function showQRCodeUpdateDiv(id)
{
  const CSRF_TOKEN = $('[name="_token"]').val();
  $.ajax({
    type:'POST',
    url:'/getAllAlternateCodes',
    data: {_token: CSRF_TOKEN, 'vcardid' : id},
    success:function(response) {
      console.log(response);
      if(response.status == 'success')
      {
        $('#alternateCardTitle').html('Add Alternate Codes for '+response.name+' Card Code: ('+response.uniqueCode+')');
        $('#vcardidAlternateCodes').val(response.vcardid);
        $('#alternateCode').val(response.premiumCode);
        $('#mobileStickerCode').val(response.mobileStickerCode);
        $('#tentCardCode').val(response.tentCardCode);
        $('#addAlternateCodes').css('display','block');
      }
      else{
        console.log("Error in fetching alternate codes");
        console.log(response);
      }
    }
  });
}

function updateAlternateCard()
{
  console.log("Update Alternate Card");
  const CSRF_TOKEN = $('[name="_token"]').val();
  const type = 'alternateCode';
  const vcardid = $('#vcardidAlternateCodes').val();
  const value = $('#alternateCode').val();
  if(value.length !== 12 || value.slice(-4) != 'SMVC')
  {
    showToast("The Alternate Code should be 12 characters long and ends in SMVC");
    return false;
  }

  const reply = updateCode(CSRF_TOKEN, type, vcardid, value);
}

function updateMobileStickerCode()
{
  console.log("Update Mobile Sticker Code");
  const CSRF_TOKEN = $('[name="_token"]').val();
  const type = 'mobileStickerCode';
  const vcardid = $('#vcardidAlternateCodes').val();
  const value = $('#mobileStickerCode').val();
  if(value.length !== 12 || value.slice(-4) != 'SMMS')
  {
    showToast("The Mobile Sticker Code should be 12 characters long and ends in SMMS");
    return false;
  }

  const reply = updateCode(CSRF_TOKEN, type, vcardid, value);
}

function updateTentCardCode()
{
  console.log("Update Tent Card Code");
  const CSRF_TOKEN = $('[name="_token"]').val();
  const type = 'tentCardCode';
  const vcardid = $('#vcardidAlternateCodes').val();
  const value = $('#tentCardCode').val();
  if(value.length !== 12 || value.slice(-4) != 'SMTC')
  {
    showToast("The Tent Card Code should be 12 characters long and ends in SMTC");
    return false;
  }

  const reply = updateCode(CSRF_TOKEN, type, vcardid, value);
}

function showToast(str){
  var toast = document.getElementById("toast");
  toast.innerHTML = str;
  toast.className = "toast show";
  setTimeout(function(){ 
      toast.className = toast.className.replace("show", ""); 
  }, 3000); // Toast will disappear after 3 seconds
}

function updateCode(CSRF_TOKEN, type, vcardid, value){
  $.ajax({
    type:'POST',
    url:'/addAlternateQR',
    data: {_token: CSRF_TOKEN, 'vcardid' : vcardid, 'type' : type, 'value' : value},
    success:function(response) {
      console.log(response);
      if(response.status == 'success')
      {
        $('#addAlternateCodes').css('display','none');
        showToast("Alternate Code Updated Successfully");
      }
      else{
        console.log("Error in fetching alternate codes");
        console.log(response);
      }
    }
  });
}
/******Earlier functions, can be deleted */
