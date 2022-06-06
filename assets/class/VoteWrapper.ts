type VoteCheckResponse = {
  hasVoted: boolean,
  positive: boolean | null
}

type VoteActionResponse = {
  hasWorked: boolean,
  newScore: number
}

export default class VoteWrapper {
  private selectedClassName: string = 'selected';

  public votingWrapper: HTMLElement;
  public scoreSpan: HTMLElement;
  public urls: Array<string> = [];
  public voteElements: Array<HTMLElement> = [];

  constructor(public wrapperElement: HTMLElement) {
    
    this.votingWrapper = this.wrapperElement.querySelector('.voting-wrapper');
    this.scoreSpan = this.wrapperElement.querySelector('.score');

    this.urls = [
      this.wrapperElement.dataset.downvoteUrl,
      this.wrapperElement.dataset.upvoteUrl
    ];
        
    this.urls.forEach((url: string, key: number) => {
      const positive = Boolean(key);

      const element = document.createElement('div');
      element.classList.add('vote');
      element.innerHTML = positive ? '<i class="fa-solid fa-circle-chevron-up"></i>' : '<i class="fa-solid fa-circle-chevron-down"></i>';
      element.onclick = async () => {
        const response: VoteActionResponse = await fetch(url).then(res => res.json());

        this.scoreSpan.textContent = String(response.newScore);
        this.votingWrapper.childNodes.forEach((element: HTMLElement) => element.classList.remove(this.selectedClassName));
        element.classList.add(this.selectedClassName);
      }

      this.votingWrapper.appendChild(element);
      this.voteElements[key] = element;
    });

    fetch(this.wrapperElement.dataset.voteCheckUrl).then(res => res.json()).then((response: VoteCheckResponse) => {
      if (response.hasVoted && response.positive !== null) this.voteElements[Number(response.positive)].classList.add(this.selectedClassName);
    })
  }
}