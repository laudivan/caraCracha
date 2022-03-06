    // Validator Object
    var valid = new Object();

    // REGEX Elements

        // matches zip codes
        valid.zipCode = /\d{5}(-\d{4})?/;

        // matches $17.23 or $14,281,545.45 or ...
        valid.Currency = /\$\d{1,3}(,\d{3})*\.\d{2}/;

        // matches 5:04 or 12:34 but not 75:83
        valid.Time = /^([1-9]|1[0-2]):[0-5]\d$/;

        //matches email
        valid.emailAddress = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/;

        // matches phone ###-###-####
        valid.phoneNumber = /^\(?\d{3}\)?\s|-\d{3}-\d{4}$/;

        // International Phone Number
        valid.phoneNumberInternational = /^\d(\d|-){7,20}/;

        // IP Address
        valid.ipAddress = /^((25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$/;

        // Date xx/xx/xxxx
        valid.Date = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/;

        // State Abbreviation
        valid.State = /^(AK|AL|AR|AZ|CA|CO|CT|DC|DE|FL|GA|HI|IA|ID|IL|IN|KS|KY|LA|MA|MD|ME|MI|MN|MO|MS|MT|NB|NC|ND|NH|NJ|NM|NV|NY|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VA|VT|WA|WI|WV|WY)$/i;

        // Social Security Number
        valid.SSN = /^\d{3}\-\d{2}\-\d{4}$/;

    
    function validateForm(theForm) {

        var elArr = theForm.elements; 

        for(var i = 0; i < elArr.length; i++) {

           with(elArr[i]) { 

              var v = elArr[i].validator; 

              if(!v) continue; 

              var thePat = valid[v]; 

              var gotIt = thePat.exec(value); 

              if(! gotIt){
                 alert(name + ": failure to match " + v + " to " + value);                  
                 elArr[i].select();
                 elArr[i].focus(); 
                 return false;
              }
           }
        }

        return true;

    }