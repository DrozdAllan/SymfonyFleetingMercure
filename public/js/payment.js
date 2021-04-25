const stripe = Stripe(stripePublicKey);

// Disable the button until we have Stripe set up on the page
document.querySelector("button").disabled = true;
fetch("/create.php", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify(purchase),
})
  .then(function (result) {
    return result.json();
  })
  .then(function (data) {
    const elements = stripe.elements();
    const card = elements.create("card");
    // Stripe injects an iframe into the DOM
    card.mount("#card-element");
    card.on("change", function (event) {
      // Disable the Pay button if there are no card details in the Element
      document.querySelector("button").disabled = event.empty;
      document.querySelector("#card-error").textContent = event.error
        ? event.error.message
        : "";
    });
    const form = document.getElementById("payment-form");
    form.addEventListener("submit", function (event) {
      event.preventDefault();
      // Complete payment when the submit button is clicked
      payWithCard(stripe, card, data.clientSecret);
    });
  });
