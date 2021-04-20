// 									CONNEXION ET ECOUTE AU HUB

const eventSource = new EventSource(hub); // Abonnement aux updates

eventSource.onmessage = ({ data }) => {
  // On écoute les événements publiés par le Hub

  const message = JSON.parse(data); // Le contenu des événements est sous format JSON, il faut le parser

  if (message.user.username === sessionuser) {
    $("div[data-channel=" + message.channel.id + "] .chatText").append(
      `<div class='row float-right'>
                <span> ${message.user.username} </span>
            <p class='alert alert-info w-100'> ${message.content} </p>
            </div>`
    );
  } else {
    $("div[data-channel=" + message.channel.id + "] .chatText").append(
      `<div class='row float-left'>
            <span> ${message.user.username} </span>
            <p class='alert alert-success w-100'> ${message.content} </p>
            </div>`
    );
  }

  $(".chatText").scrollTop($(".chatText").height()); // Scroll after message received
};

//												 ENVOIE AU HUB

$("button.buttonChat").click(function (e) {
  e.preventDefault(); // Empêche la page de se rafraîchir après le submit du formulaire

  const inputBeforeButton = $(this).siblings("input");
  const message = inputBeforeButton[0].value;
  const channelNb = $(this).parents(".tab-pane").data("channel");

  const data = {
    content: message,
    channel: channelNb,
  };

  inputBeforeButton[0].value = ""; // Efface le message dans l'input

  fetch("/message", {
    // Envoie du form en JSON vers le controller de messages
    method: "POST",
    body: JSON.stringify(data), // On envoie les data sous format JSON
  })
    .then((response) => {
      if (response.ok) {
        return response;
      } else {
        throw new Error("Something went wrong");
      }
    })
    .then((response) => {
      console.log(response);
    });
});
