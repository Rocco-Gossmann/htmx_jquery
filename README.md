# JQuery + HTMX - Demo

a short project, to check, how JQuery and HTMX can interact with each other.

turns out, it is supprisingly simple.







# Further notes

## Creating HTMX-Elements via JQuery

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


## Using JQuery to make via HTMX returned Markup interactive.

That is a bit tricky, but there are different options.

### `hx-on::after-request`

https://htmx.org/attributes/hx-on/

if you set a `hx-on::after-request` attribute, the `CustomEvent` passed to it, will contain both
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

### JQuerys `decendet-selector`

https://api.jquery.com/on/

When manipulating events via JQuery it is possible to assign events to a parent
element whos decendants will then become the interactive elements.

For example:

```javascript
$(document).on("click", "BUTTON", () => console.log("button clicked"));
```

In this case the event is registered on the `document` but it will only trigger, if
any `BUTTON` within the `document` is clicked.
That way it does not matter how many buttons are added or removed since the event
is bound to the buttons container.

Unlike

```javascript
$("BUTTON").on("click", () => ... );
```

which is maybe a bit less performance hungy on click, but requires an execution
when ever Buttons are added or removed in the DOM.
