# Svelte-DragDropList

Sortable lists [made with Svelte](https://madewithsvelte.com/svelte-dragdroplist).  [Available from NPM!](https://www.npmjs.com/package/svelte-dragdroplist)  

### Why this component?

* Bidirectional binding - data order updates as soon as the user drags a list item into a new position, even before it is dropped
* Full touch support - doesn't use the HTML5 drag and drop API
* Accessible - includes buttons to move elements without dragging
* Easier than writing a new one, probably.

### Usage

[Basic REPL](https://svelte.dev/repl/6fb61b9868734493aec65eb53dc1a4bd?version=3.22.2)  
[REPL with every feature!](https://svelte.dev/repl/915db3b3ed704fddb7ddfb64bcbc2624?version=3.22.2)  

The simplest way to use the component is to pass it an array of unique strings.  If you `bind:data`, the source array will be updated as the user rearranges its items.
```js
<script>
    import DragDropList from "svelte-dragdroplist";

    let data = ["Adams", "Boston", "Chicago", "Denver"];
</script>

<DragDropList bind:data={data}/>
```

##### Unique IDs

If you aren't sure that your strings will be unique, you should instead pass an array of objects, each with a unique ID:  

```js
let data = [{"id": 0, "text": "Boston"}, 
            {"id": 1, "text": "Boston"}, 
            {"id": 2, "text": "Chicago"}, 
            {"id": 3, "text": "Denver"}];
```

##### HTML

You can also include an "html" attribute instead of "text".  It's up to you to make sure the html is clean.  
  If you want, you can even use both in one list.  
```js
let data = [{"id": 0, "text": "Adams"}, 
            {"id": 1, "text": "Boston"}, 
            {"id": 2, "html": "<p style='color: blue;'>Chicago</p>"}, 
            {"id": 3, "html": "<p style='color: red;'>Denver</p>"}];
```

##### Removable Items

A delete button can be added to each item with the `removesItems` prop:
```js
<DragDropList bind:data={data} removesItems={true}/>
```
Note: _adding_ items is as simple as adding them to the data array.

### Styling

To style the list and its elements from a parent component or global stylesheet, prefix your selectors with `.dragdroplist`.  You may need to increase the specificity of your selectors or even use the `!important` rule in order to override the classes applied by Svelte.  For example:

```css
:global(.dragdroplist) {} /* entire component */
:global(.dragdroplist > .list > div.item) {} /* list item */
:global(.dragdroplist div.buttons > button.down) {} /* move down button */
:global(.dragdroplist div.content) {} /* text/html contents of item */
```

If you only need to style the contents of an item, you can also use an object with an `html` property as described above.

### In Progress

* Additional animations on drop  