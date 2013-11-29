package ladanse.website.bean;

import ladanse.website.domain.Account;
import ladanse.website.domain.Event;
import ladanse.website.domain.master.RoleType;
import ladanse.website.domain.master.SignUpType;
import org.apache.logging.log4j.LogManager;
import org.apache.logging.log4j.Logger;

import javax.enterprise.context.RequestScoped;
import javax.inject.Inject;
import javax.inject.Named;
import javax.naming.Context;
import javax.naming.InitialContext;
import javax.naming.NamingException;
import javax.persistence.EntityManager;
import javax.sql.DataSource;
import java.util.Date;
import java.util.HashSet;
import java.util.Random;
import java.util.Set;

@Named
@RequestScoped
public class TestJPABean {

    static private Logger logger = LogManager.getLogger(TestJPABean.class.getName());

    @Inject
    private JpaSessionBean jpaSessionBean;

    public void testJPAButtonClicked() {

        System.out.println("Old System.out");

        logger.entry();
        logger.info("testJPAButtonClicked()");

        try {
            EntityManager entityManager = jpaSessionBean.getEntityManager();

            if (entityManager == null) {
                logger.info("EntityManager was null");

                return;
            }

            entityManager.getTransaction().begin();

            Random rnd = new Random();

            Account account = new Account();
            account.setId(Math.abs(rnd.nextLong()));

            Event event = new Event(account);

            event.setName("ToT Gear Up");
            event.setInviteTime(new Date());
            event.setStartTime(new Date());

            Set<RoleType> roleTypes = new HashSet<>();
            roleTypes.add(RoleType.Healer);
            roleTypes.add(RoleType.Tank);

            event.signUp(account, SignUpType.WillCome, roleTypes);

            entityManager.persist(account);
            entityManager.persist(event);

            entityManager.getTransaction().commit();
        } catch (Throwable t) {
            t.printStackTrace();
        }
    }
}
