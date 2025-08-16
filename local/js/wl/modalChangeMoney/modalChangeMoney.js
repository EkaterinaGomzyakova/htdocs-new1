function showCalculator(data) {
   try {
      let price = parseFloat(data.price);
      let priceLeft = parseFloat(data.priceLeft);
      let amountPaid = parseFloat(price - priceLeft);

      const recalculateFromOrderSum = (e) => {
         const newPrice = parseFloat(e.target.value);
         if (isNaN(newPrice)) return;
         price = newPrice;
         const result = (price - amountPaid) * -1;
         document.getElementById("amountDue").value = BX.Currency.currencyFormat(result, 'RUB');
      };

      const recalculateFromAmountPaid = (e) => {
         const newAmountPaid = parseFloat(e.target.value);
         if (isNaN(newAmountPaid)) return;
         amountPaid = newAmountPaid;
         const result = (price - amountPaid) * -1;
         document.getElementById("amountDue").value = BX.Currency.currencyFormat(result, 'RUB');
      };

      const Dialog = new BX.CDialog({
         title: "Калькулятор сдачи",
         content: `
          <div class="modal" id="modalChangeMoney">
             <label for="orderSum">Сумма заказа:</label>
             <input  id="orderSum" type="number" placeholder="Введите сумму заказа" value="${price}">
             <label for="amountPaid">Внесено:</label>
             <input  id="amountPaid" type="number" placeholder="Введите внесенную сумму" value="${amountPaid}">
             <label for="amountDue">Сдача</label>
             <input  id="amountDue" placeholder="Сумма к оплате" readonly value="${BX.Currency.currencyFormat(priceLeft, 'RUB')}">
         </div>`,
         icon: "head-block",
         resizable: true,
         draggable: true,
         height: "300",
         width: "400",
      });

      document
        .querySelector("#modalChangeMoney #orderSum")
        .addEventListener("input", recalculateFromOrderSum);
      document
        .querySelector("#modalChangeMoney #amountPaid")
        .addEventListener("input", recalculateFromAmountPaid);

      Dialog.SetButtons([
         {
            title: "Закрыть",
            id: "cancel",
            name: "cancel",
            action: function () {
               this.parentWindow.Close();
            },
         },
      ]);

      Dialog.Show();
   } catch (e) {
      console.log(e.message);
   }
}
