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
  private waitingClassName: string = 'waiting';

  public votingWrapper: HTMLElement;
  public scoreSpan: HTMLElement;
  public votingUrls: Array<string> = [];
  public voteElements: Array<HTMLElement> = [];
  public removeVoteUrl: string;
  public vote?: boolean = null;

  constructor(public wrapperElement: HTMLElement) {
    
    this.votingWrapper = this.wrapperElement.querySelector('.voting-wrapper');
    this.scoreSpan = this.wrapperElement.querySelector('.score');

    this.votingUrls = [
      this.wrapperElement.dataset.downvoteUrl,
      this.wrapperElement.dataset.upvoteUrl
    ];

    this.removeVoteUrl = this.wrapperElement.dataset.removeVoteUrl;
        
    this.votingUrls.forEach((url: string, key: number) => {
      const positive = Boolean(key);

      const element = document.createElement('div');
      element.classList.add('vote');
      element.innerHTML = positive ? '<i class="fa-solid fa-circle-chevron-up"></i>' : '<i class="fa-solid fa-circle-chevron-down"></i>';
      element.onclick = async () => {
        // If this.vote is already equal to the vote element's positivity, then we need to remove the vote
        // With front-end pseudo-refresh
        if (this.vote === positive) {
          this.vote = null;
          var conditionalUrl = this.removeVoteUrl;

          this.scoreSpan.textContent = String(parseInt(this.scoreSpan.textContent) - ((key * 2) - 1));
        } else {
          if (this.vote === !positive) {
            this.scoreSpan.textContent = String(parseInt(this.scoreSpan.textContent) + ((key * 2) - 1) * 2);
          }
          else {
            this.scoreSpan.textContent = String(parseInt(this.scoreSpan.textContent) + ((key * 2) - 1));
          }

          var conditionalUrl = url;
          this.vote = positive;
        }

        element.classList.add(this.waitingClassName);
        

        this.resetSelectedVoteElements();
        if (this.vote === positive) element.classList.add(this.selectedClassName);


        // Actual fetching then real refreshing
        const response: VoteActionResponse = await fetch(conditionalUrl).then(res => res.json());

        this.scoreSpan.textContent = String(response.newScore);
        this.resetSelectedVoteElements();
        if (this.vote === positive) element.classList.add(this.selectedClassName);
        element.classList.remove(this.waitingClassName);
      }

      this.votingWrapper.appendChild(element);
      this.voteElements[key] = element;
    });

    fetch(this.wrapperElement.dataset.voteCheckUrl).then(res => res.json()).then((response: VoteCheckResponse) => {
      if (response.hasVoted && response.positive !== null) this.voteElements[Number(response.positive)].classList.add(this.selectedClassName);
      this.vote = response.positive;
    })
  }

  private resetSelectedVoteElements() {
    this.votingWrapper.childNodes.forEach((element: HTMLElement) => element.classList.remove(this.selectedClassName));
  }
}