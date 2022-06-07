import csv
import json
import os
import time
import re
from colorama import Fore, Back, Style

ROOT_DIR = os.path.realpath(os.path.join(os.path.dirname(__file__)))
SRC_DIR = ROOT_DIR + '/src'
OUTPUT_DIR = ROOT_DIR + '/output'

with open(SRC_DIR + '/train.json', 'r', encoding='utf-8') as f:
  data = json.load(f)

uniqueIngredientsDict = dict()

for entry in data:
  for ingredient in entry['ingredients']:
    uniqueIngredientsDict[ingredient] = uniqueIngredientsDict[ingredient] + 1 if ingredient in uniqueIngredientsDict else 1

uniqueIngredientsDict = {k: v for k, v in sorted(uniqueIngredientsDict.items(), key=lambda item: item[1])}

uniqueIngredientsList = []
for name, occurrences in uniqueIngredientsDict.items():
  uniqueIngredientsList.append({'name': name, 'occurrences': occurrences})
uniqueIngredientsList.reverse()

uniqueIngredientsList = uniqueIngredientsList[:100]

for index, ingredient in enumerate(uniqueIngredientsList):
  for comparedIndex, comparedIngredient in enumerate(uniqueIngredientsList):
    if (
        comparedIngredient in uniqueIngredientsList and
        ingredient != comparedIngredient and
        re.compile(".*(" + ingredient['name'] + ").*").match(comparedIngredient['name'])
    ):
      userInput = input(('Remove possible "%s" duplicate "%s"? (y / N / r to keep duplicate and remove original) ' % (
        Fore.GREEN + ingredient['name'] + Fore.RESET,
        Fore.YELLOW + comparedIngredient['name'] + Fore.RESET,
      ))).lower()

      if userInput in ['y', 'yes']:
        uniqueIngredientsList.remove(comparedIngredient)
        print(Fore.RED + ('Removed "%s".' % comparedIngredient['name']) + Fore.RESET)
      elif userInput in ['r', 'replace']:
        uniqueIngredientsList.remove(ingredient)
        print(Fore.RED + ('Removed "%s".' % ingredient['name']) + Fore.RESET)
        break


with open(OUTPUT_DIR + '/ingredients.' + str(time.time()) + '.csv', 'w', encoding='utf-8', newline='\n') as csvfile:
  writer = csv.DictWriter(csvfile, fieldnames=['name', 'occurrences'])
  writer.writeheader()
  for ingredient in uniqueIngredientsList:
    writer.writerow({ 'name': ingredient['name'], 'occurrences': ingredient['occurrences'] })