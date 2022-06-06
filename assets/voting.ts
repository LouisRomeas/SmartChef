import VoteWrapper from "./class/VoteWrapper";

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-score-wrapper]').forEach((wrapperElement: HTMLElement) => {
    const voteWrapper = new VoteWrapper(
      wrapperElement,
    );
    console.log(voteWrapper)
  })
});