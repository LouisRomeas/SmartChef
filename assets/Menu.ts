export default class Menu {
  constructor(
    public menuElement: HTMLElement,
    public menuHandleElements: {
      open: HTMLElement,
      close: HTMLElement
    },
    public shadowOnPageElement: HTMLElement
  ) {
    this.menuHandleElements.open.onclick = () => this.toggleSideMenu(false);
    this.menuHandleElements.close.onclick = () => this.toggleSideMenu(true);
  }

  public toggleSideMenu(close: boolean = false) {
    if (close) {
      this.menuElement.classList.remove('expanded');
      this.shadowOnPageElement.classList.remove('shown');
    } else {
      this.shadowOnPageElement.classList.add('shown');
      this.menuElement.classList.add('expanded');
    }
  }
}