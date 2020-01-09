## Customer Database module

This module provides a simple customer database for tracking visitor behaviour across sites and other
channels via Segment.com

 * /segment-webhook
 * /api/customer-data/ 

## Getting started

This is a very simple PoC but here is some sample code to give an idea:

### Setting up a Segment.com account

 * Create a segment.com account
 * Create a JavaScript source
 * Create a webhook destination
 * Set the URL to http://yoursite.example.com/segment-webhook


### On your website

First put the segment.com tracking code into your page template (yes, the module should be refactored to insert this itself, probably with the tagmanager module)

```html
<script>
  !function(){var analytics=window.analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on"];analytics.factory=function(t){return function(){var e=Array.prototype.slice.call(arguments);e.unshift(t);analytics.push(e);return analytics}};for(var t=0;t<analytics.methods.length;t++){var e=analytics.methods[t];analytics[e]=analytics.factory(e)}analytics.load=function(t,e){var n=document.createElement("script");n.type="text/javascript";n.async=!0;n.src=("https:"===document.location.protocol?"https://":"http://")+"cdn.segment.com/analytics.js/v1/"+t+"/analytics.min.js";var o=document.getElementsByTagName("script")[0];o.parentNode.insertBefore(n,o);analytics._loadOptions=e};analytics.SNIPPET_VERSION="4.1.0";
  analytics.load("<your ID here>");
  analytics.page();
  }}();
</script>
```

Then put some script in to pass identifier details:
```html
<script>
  analytics.ready(function() {
    <% if $CurrentMember %>
    analytics.identify('$CurrentMember.ID', {
        email: '$CurrentMember.Email.JS',
        firstName: '$CurrentMember.FirstName.JS',
        lastName: '$CurrentMember.Surname.JS'
    });
    <% end_if %>
    <% if $URLSegment = "contact-us" %>
    analytics.track("visit-contact-us");
    <% end_if %>
  });
</script>
```

Finally, add some script (in a js file or inline) that uses the customer DB to get into about the current visitors:

```html
<script>
    analytics.ready(function() {
        var anonId = analytics.user().anonymousId();
        jQuery.getJSON('http://yoursite.example.com/api/customer-info/' + anonId, function(data) {
            if (data.firstName) {
                $('#header-subtitle').text("Hello, " + data.firstName).attr('style', 'color: red');
            }
        });

        jQuery('#customer-anonymous-id').attr('href', '#');
    })

    jQuery('#customer-anonymous-id').click(function() {
        var anonId = analytics.user().anonymousId();

        jQuery.getJSON('http://yoursite.example.com/api/customer-info/' + anonId, function(data) {
            html = 'Anonymous ID: ' + anonId + '<br>';
            for(k in data) {
                html += '<b>' + k + ':</b> ' + data[k] + '<br>';
            }
            jQuery('#customer-info')
                .html(html)
                .show();
        });

        return false;
    });
</script>
```
