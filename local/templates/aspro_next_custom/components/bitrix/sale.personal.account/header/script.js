$('[data-toggle="tooltip"]').tooltip()

class BonusPoints extends HTMLElement {
  constructor () {
    super()

    this._isActual = false
    this._value = null
  }

  connectedCallback () {
    this._value = this.getAttribute('value')
    this._isActual = this.getAttribute('is-actual') === '1'
    if (!this._isActual) {
      setTimeout(() => {
        this.load()
      }, 30000)
    }
    this.render()
  }

  async load () {
    try {
      const { data } = await BX.ajax.runAction('wl:onec_loyalty.Bonus.load')
      this._isActual = data.isActual
      this._value = data.value
      this.render()
    } catch (e) {

    }

    if (!this._isActual) {
      setTimeout(() => {
        this.load()
      }, 30000)
    }
  }

  render () {
    this.classList.add('bonus-points')
    //language=HTML
    this.innerHTML = `${this._value}`
  }
}

customElements.define('bonus-points', BonusPoints)