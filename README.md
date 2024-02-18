# JQuery + HTMX - Demo

a short project, to check, how JQuery and HTMX can interact with each other.

turns out, it is supprisingly simple.

# How to run it

The whole test can be run via a PHP-Development Server.

install the PHP and PHP-CLI for your System (min. PHP 7.4)

Then clone this project.

```bash
git clone https://github.com/rocco-gossmann/htmx_jquery
```

Enter the directory you just cloned

```bash
cd htmx_jquery
```

Run the PHP-Development Server

```bash
php -S localhost:3753
```

goto http://localhost:3753 to see if it works.

# How does it work

A click on the `click here to update Time` message will make a call to PHP-Server
`api/time.php`. The Time displayed always comes from the Server.

A cookie will keep track of how often the API-Script has been called.

the API will then return some markup, that is put into the Document via HTMX.

Returned Markup also contain a Button, whos interactionn is handled via JQuery.

# Creating HTMX-Elements via JQuery

To createHTMX elements, via JQuery, you would do it as you would with any other element.

```javascript
const $trigger = $(`
    <div class="myTriggerElement"
         hx-get="/api/time.php" 
         hx-target=".time-target" 
         hx-headers="{&quot;SomeInsecureHeader&quot;: &quot;*giggles* I'm in danger (For the love of god, don't put security relevant headers here)&quot;}"  
         hx-trigger="click, load"
    > Click here to update Time </div>
`);
```

But to activate their HTMX functionality, you need to first append them to the Body.

```javascript
$("BODY").append($target);
```

and then call `htmx.process` on directly on the added DOM element

```javascript
htmx.process($trigger[0]);
```

# Using JQuery to make via HTMX returned Markup interactive.

That is a bit tricky, but there are different options.

## `hx-on::after-request`

https://htmx.org/attributes/hx-on/

if you set a `hx-on::after-request attribute`, the `CustomEvent` passed to it, will contain both
the request, as well as the DOM-Target.

```javascript
function onAfterRequest(ev) {
    console.log("Request", ev.detail.xhr);
    console.log("DOM-Target-Element: ", ev.detail.target);

    if (ev.detail.xhr.status == 200) {
        $(ev.detail.target)
            .find(".msg-button")
            .on("click", function (ev) {
                alert($(ev.target).data("msg"));
            });
    }
}
```

All that is left, is to give your HTMX-Element the Event-Attribute.

```html
<div ... ... hx-on::after-request="onAfterRequest(event)">...</div>
```

## JQuerys `decendet-selector`

https://api.jquery.com/on/

When manipulating events via JQuery it is possible to assign events, to a parent
element whos decendents will then become the interactive elements

for example:

```javascript
$(document).on("click", "BUTTON", () => console.log("button clicked"));
```

in this case the event is registered on the `document`, but it will only trigger, if
any `BUTTON` within the `document` is clicked.
That way, it does not matter, how many buttons are added or removed, since the event
is not bound to the button, but the container, the button is in.

Unlike

```javascript
$("BUTTON").on("click", () => ... );
```

which is a bit more less performance hungy maybe, but requires an execution,
when ever Buttons are added or removed in the DOM.
