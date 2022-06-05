import autocomplete, { AutocompleteItem } from "autocompleter";

type Ingredient = {
  id: number,
  label: string,
  emoji?: string,
  unit: string
}

document.addEventListener('DOMContentLoaded', () => {
  const inputTextElement: HTMLInputElement = document.getElementById('ingredient-picker') as HTMLInputElement;
  const prototypeElement = document.getElementById('recipe_recipeIngredients');
  const subFormParent = prototypeElement.closest('div:not(#recipe_recipeIngredients');

  subFormParent.classList.add('recipe-ingredients-list');
  
  autocomplete({
    input: inputTextElement,
    fetch: async (text, update) => {
      const params = new URLSearchParams({ query: text });
      const url = inputTextElement.dataset.ingredientsQueryUrl;
  
      const response: Ingredient[] = await fetch(url + '?' +params.toString()).then(res => res.json());

      response.forEach(ingredient => ingredient.label = (ingredient.emoji ?? '🍴') + ingredient.label)
  
      update(response);
    },
    onSelect: (ingredient: Ingredient) => {
  
      const counter = parseInt(inputTextElement.dataset.index) + 1;
  
      const prototypeHTML = prototypeElement.dataset.prototype.replace(
        /__name__/g,
        String(counter)
      );
  
      inputTextElement.dataset.index = String(counter);
  
      const tempWrapper = document.createElement('div');
      tempWrapper.innerHTML = prototypeHTML;
      const newFormEntryElement = tempWrapper.querySelector(':first-child');
      newFormEntryElement.classList.add('recipe-ingredient-subform');
      subFormParent.appendChild(newFormEntryElement);
  
      newFormEntryElement.querySelector('label').textContent = ingredient.label;

      const removeSubFormButton: HTMLElement = document.createElement('div');
      removeSubFormButton.classList.add('remove-subform');
      removeSubFormButton.innerHTML = '<i class="fa-solid fa-trash"></i>';
      removeSubFormButton.onclick = (e: Event) => {
        e.preventDefault();

        newFormEntryElement.remove();
      }
      newFormEntryElement.appendChild(removeSubFormButton);

      const ingredientLabel: HTMLLabelElement = newFormEntryElement.querySelector('label');
      ingredientLabel.classList.add('ingredient-label');
  
      const quantityInput: HTMLInputElement = newFormEntryElement.querySelector('input[name*=quantity]');
      quantityInput.type = 'number';
      quantityInput.value = quantityInput.min = String(1);

      // Add +/- buttons to quantityInput
      [true, false].forEach((positive: boolean) => {
        const button = document.createElement('span');
        button.onclick = (e: Event) => {
          e.preventDefault();
          if (quantityInput.dataset.locked) return;

          let quantity: number = parseInt(quantityInput.value) ?? 0;
          quantity += (positive ? +1 : -1);
          if (quantity < parseInt(quantityInput.min)) quantity = parseInt(quantityInput.min);
          quantityInput.value = String(quantity);
          quantityInput.dataset.locked = 'true';
          setTimeout(() => {
            delete quantityInput.dataset.locked;
          }, 20);
        }
        button.innerHTML = `<i class="fa-solid fa-square-${positive ? 'plus' : 'minus'}"></i>`;
        button.classList.add('quantity-increment')
        quantityInput.parentElement.insertBefore(button, quantityInput);
      });

      // Add span with unit after quantityInput
      const unitSpan = document.createElement('span');
      unitSpan.classList.add('unit');
      unitSpan.textContent = ingredient.unit;
      quantityInput.parentElement.appendChild(unitSpan);

      quantityInput.parentElement.classList.add('quantity');


      const isOptionalInput: HTMLInputElement = newFormEntryElement.querySelector('input[name*=isOptional');
      isOptionalInput.classList.add('checkbox-first');
      isOptionalInput.parentElement.classList.add('is-optional');
      

      const ingredientSelect: HTMLSelectElement = newFormEntryElement.querySelector('select[name*=ingredient]');
      
      const ingredientInput: HTMLInputElement = document.createElement('input');
      [ingredientInput.id, ingredientSelect.id] = [ingredientSelect.id, ingredientInput.id];
      ingredientInput.name = ingredientSelect.name;
      ingredientInput.type = 'hidden';
      ingredientInput.value = String(ingredient.id);

      ingredientSelect.parentElement.appendChild(ingredientInput);
      ingredientSelect.remove();
  
      inputTextElement.value = '';
    },
    className: 'autocomplete-container',
    preventSubmit: true
  })
});