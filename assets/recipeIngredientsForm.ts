import autocomplete from "autocompleter";

type Ingredient = {
  id: number,
  label: string,
  emoji?: string,
  unit: string
}

const defaultEmoji = '🍴';

document.addEventListener('DOMContentLoaded', () => {
  const inputTextElement: HTMLInputElement = document.getElementById('ingredient-picker') as HTMLInputElement;
  const prototypeElement = document.getElementById('recipe-ingredients-subform-prototype');
  const subFormParent = prototypeElement.parentElement;
  subFormParent.classList.add('recipe-ingredients-list');
  
  const formatRecipeIngredientSubForm = (subFormElement: HTMLElement, ingredient: Ingredient): HTMLElement => {
    subFormElement.classList.add('recipe-ingredient-subform');
    
    const removeSubFormButton: HTMLElement = document.createElement('div');
    removeSubFormButton.classList.add('remove-subform');
    removeSubFormButton.innerHTML = '<i class="fa-solid fa-trash"></i>';
    removeSubFormButton.onclick = (e: Event) => {
      e.preventDefault();
      
      subFormElement.remove();
      inputTextElement.dataset.index = String(parseInt(inputTextElement.dataset.index) - 1);
    }
    subFormElement.appendChild(removeSubFormButton);
    
    // Ingredient name label
    const ingredientLabel: HTMLLabelElement = subFormElement.querySelector('label');
    ingredientLabel.textContent = (ingredient.emoji ?? defaultEmoji) + ingredient.label;
    ingredientLabel.classList.add('ingredient-label');
  
    // Quantity Input
    const quantityInput: HTMLInputElement = subFormElement.querySelector('input[name*=quantity]');
    quantityInput.type = 'number';
    if (!quantityInput.value) quantityInput.value = quantityInput.min = String(1);

    {  
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
    }
  
    const isOptionalInput: HTMLInputElement = subFormElement.querySelector('input[name*=isOptional');
    isOptionalInput.classList.add('checkbox-first');
    isOptionalInput.parentElement.classList.add('is-optional');
    
  
    const ingredientSelect: HTMLSelectElement = subFormElement.querySelector('select[name*=ingredient]');
    
    const ingredientInput: HTMLInputElement = document.createElement('input');
    [ingredientInput.id, ingredientSelect.id] = [ingredientSelect.id, ingredientInput.id];
    ingredientInput.name = ingredientSelect.name;
    ingredientInput.type = 'hidden';
    ingredientInput.value = String(ingredient.id);
  
    ingredientSelect.parentElement.appendChild(ingredientInput);
    ingredientSelect.remove();
  
    return subFormElement;
  }
  
  Array.from(subFormParent.children).forEach((subFormElement: HTMLElement) => {
    const rowWrapper = subFormElement.querySelector('div');
    if (!rowWrapper) return;

    const ingredient: Ingredient = {
      id: parseInt(rowWrapper.dataset.id),
      label: rowWrapper.dataset.label,
      emoji: rowWrapper.dataset.emoji,
      unit: rowWrapper.dataset.unit
    }

    subFormElement = formatRecipeIngredientSubForm(subFormElement, ingredient);

  });
  
  autocomplete({
    input: inputTextElement,
    fetch: async (text, update) => {
      const params = new URLSearchParams({ query: text });
      const url = inputTextElement.dataset.ingredientsQueryUrl;
  
      const response: Ingredient[] = await fetch(url + '?' +params.toString()).then(res => res.json());

      // Concatenate emoji to beginning of label, for dropdown menun display purposes
      response.forEach(ingredient => ingredient.label = (ingredient.emoji ?? defaultEmoji) + ingredient.label)
  
      update(response);
    },
    onSelect: (ingredient: Ingredient) => {
      // Remove emoji from label, it will be added back properly in the formatting method
      ingredient.label = ingredient.label.replace(ingredient.emoji ?? defaultEmoji, '');
  
      const counter = parseInt(inputTextElement.dataset.index) + 1;
  
      const prototypeHTML = prototypeElement.dataset.prototype.replace(
        /__name__/g,
        String(counter)
      );
  
      inputTextElement.dataset.index = String(counter);
  
      const tempWrapper = document.createElement('div');
      tempWrapper.innerHTML = prototypeHTML;
      const newSubFormElement: HTMLElement = tempWrapper.querySelector(':first-child');
      subFormParent.appendChild(formatRecipeIngredientSubForm(newSubFormElement, ingredient));
  
      inputTextElement.value = '';
    },
    className: 'autocomplete-container',
    preventSubmit: true
  })
});