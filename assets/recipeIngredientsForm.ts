import autocomplete, { AutocompleteItem } from "autocompleter";

document.addEventListener('DOMContentLoaded', () => {
  const inputTextElement: HTMLInputElement = document.getElementById('ingredient-picker') as HTMLInputElement;
  const prototypeElement = document.getElementById('recipe_recipeIngredients');
  const subFormParent = prototypeElement.closest('div:not(#recipe_recipeIngredients');

  subFormParent.classList.add('recipe-ingredients-list')
  
  autocomplete({
    input: inputTextElement,
    fetch: async (text, update) => {
      const params = new URLSearchParams({ query: text });
      const url = inputTextElement.dataset.ingredientsQueryUrl;
  
      const response: { label: string, value: number }[] = await fetch(url + '?' +params.toString()).then(res => res.json());
  
      update(response);
    },
    onSelect: (item: { label: string, value: number }) => {
  
      const counter = parseInt(inputTextElement.dataset.index) + 1;
  
      const prototypeHTML = prototypeElement.dataset.prototype.replace(
        /__name__/g,
        String(counter)
      );
  
      inputTextElement.dataset.index = String(counter);
  
      const tempWrapper = document.createElement('div');
      tempWrapper.innerHTML = prototypeHTML;
      const newFormEntryElement = tempWrapper.querySelector(':first-child');
      subFormParent.appendChild(newFormEntryElement);
  
      newFormEntryElement.querySelector('label').textContent = item.label
  
      const quantityInput: HTMLInputElement = newFormEntryElement.querySelector('input[name*=quantity]');
      quantityInput.type = 'number';
      quantityInput.value = String(1);
  
      const ingredientSelect: HTMLSelectElement = newFormEntryElement.querySelector('select[name*=ingredient]');
      
      const ingredientInput: HTMLInputElement = document.createElement('input');
      [ingredientInput.id, ingredientSelect.id] = [ingredientSelect.id, ingredientInput.id];
      ingredientInput.name = ingredientSelect.name;
      ingredientInput.type = 'hidden';
      ingredientInput.value = String(item.value);

      ingredientSelect.parentElement.appendChild(ingredientInput);
      ingredientSelect.remove();
  
      inputTextElement.value = '';
    },
    className: 'autocomplete-container',
    preventSubmit: true
  })
});